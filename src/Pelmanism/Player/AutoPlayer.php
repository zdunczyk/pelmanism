<?php

namespace Pelmanism\Player;

use Pelmanism\Card\Card;
use Pelmanism\Move\Move;
use Pelmanism\Move\MoveResolverInterface;

/**
 * Computer player
 */
class AutoPlayer implements PlayerInterface
{

    /**
     * Cards found up to the point
     * @var array
     */
    private $found = array();

    /**
     * Cards remembered after previous selects
     * @var array
     */
    private $remembered = array();

    /**
     * Name of the player
     * @var string
     */
    private $name;

    /**
     * Returns first valid move remembered from past turns, or null otherwise
     * @param  string                $card_number Card's number
     * @param  MoveResolverInterface $resolver    Game engine
     * @return Move|null
     */
    private function getValidMove($card_number, MoveResolverInterface $resolver)
    {
        if (isset($this->remembered[$card_number])) {

            foreach ($this->remembered[$card_number] as $move_id => $move) {
                if (!$resolver->isForbidden($move_id)) {
                    return $move;
                }
            }
        }

        return null;
    }

    /**
     * Returns first remembered number
     * @param  MoveResolverInterface $resolver Game engine which knows the rules
     * @return string                          Sample card number
     */
    private function getSampleNumber(MoveResolverInterface $resolver)
    {
        foreach ($this->remembered as $number => $moves) {
            foreach ($moves as $move_id => $move) {
                if (!$resolver->isForbidden($move_id)) {
                    return $number;
                }
            }
        }
        throw new \Exception;
    }

    /**
     * Returns remembered pair of cards
     * @return array|false Array of cards
     */
    private function anyRememberedPair()
    {
        foreach ($this->remembered as $moves) {
            if (count($moves) >= 2) {
                return array_slice($moves, 0, 2);
            }
        }

        return false;
    }

    /**
     * Creates new player
     * @param string $name Player's name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Takes turn for the player
     * @param  MoveResolverInterface $resolver Game engine which knows the game rules
     * @return null
     */
    public function takeTurn(MoveResolverInterface $resolver)
    {
        if (($moves = $this->anyRememberedPair())) {
            /* pair remembered */
            $resolver->doMove(reset($moves));
            $resolver->doMove(next($moves));

        } else {
            list($first_card, $first_move) = $resolver->doRandomMove();

            try {
                /* try to declare the card */
                $decl_number = $this->getSampleNumber($resolver);
                $first_card->declareNumber($decl_number);
                $resolver->doMove($this->getValidMove($decl_number, $resolver));
            } catch (\Exception $e) {
                /* sadly it was not a joker, or there is nothing to declare, is this card remembered?  */
                if (($move = $this->getValidMove($first_card->getNumber(), $resolver))) {
                    $resolver->doMove($move);
                } else {
                    list($second_card, $second_move) = $resolver->doRandomMove();

                    try {
                        $second_card->declareNumber($first_card->getNumber());
                    } catch (\Exception $e) {
                        // ignore
                    }
                }
            }
        }
    }

    /**
     * Handles an event and remembers the card
     * @param  Card   $card Card which has been selected
     * @param  Move   $m    Move which has been made
     * @return null
     */
    public function cardPreviewed(Card $card, Move $m)
    {
        $number = $card->getNumber();

        if (!isset($this->remembered[$number])) {
            $this->remembered[$number] = array();
        }

        $this->remembered[$number][$m->getUniqueId()] = $m;
    }

    /**
     * Handles an event from game engine and tries to forget about collected card
     * @param  Card   $card Card which has been collected
     * @param  Move   $m    Collected card's position
     * @return null
     */
    public function cardCollected(Card $card, Move $m)
    {
        $number = $card->getNumber();
        $move_id = $m->getUniqueId();

        if (isset($this->remembered[$number])) {
            if (isset($this->remembered[$number][$move_id])) {
                unset($this->remembered[$number][$move_id]);

                if (empty($this->remembered[$number])) {
                    unset($this->remembered[$number]);
                }
            }
        }
    }

    /**
     * Adds cards from pair to found array
     * @param  Card   $c0 First card from pair
     * @param  Card   $c1 Second card from pair
     * @return null
     */
    public function pairFound(Card $c0, Card $c1)
    {
        $this->found[] = $c0;
        $this->found[] = $c1;
    }

    /**
     * Returns result for this player
     * @return int Player's result
     */
    public function getResult()
    {
        return count($this->found) / 2;
    }

    /**
     * Returns name of player
     * @return string Player's name
     */
    public function __toString()
    {
        return $this->name;
    }
}
