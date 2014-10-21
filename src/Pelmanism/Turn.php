<?php

namespace Pelmanism;

use Pelmanism\Card\Card;
use Pelmanism\Move\Move;

/**
 * Class which represents a game's turn
 */
class Turn
{

    /**
     * Random guess type
     */
    const RANDOM_GUESS = 0;

    /**
     * Remembered guess type
     */
    const REMEMBERED_CARD = 1;

    /**
     * Cards allowed in turn
     * @var int
     */
    private $cards_per_turn;

    /**
     * Guess made in the turn
     * @var int
     */
    private $guess_type;

    /**
     * Player who is playing in the turn
     * @var PlayerInterface
     */
    private $player;

    /**
     * Player's id
     * @var string
     */
    private $player_id;

    /**
     * Was card match successfull
     * @var boolean
     */
    private $match = false;

    /**
     * Cards taken in turn
     * @var array
     */
    private $cards = array();

    /**
     * Moves done in turn
     * @var array
     */
    private $moves = array();

    /**
     * Creates new turn
     * @param int $cards_per_turn Cards allowed in turn
     * @param PlayerInterface $player         Player playing in the turn
     * @param string $player_id      Player's id
     */
    public function __construct($cards_per_turn, $player, $player_id)
    {
        $this->cards_per_turn = $cards_per_turn;
        $this->player = $player;
        $this->player_id = $player_id;
    }

    /**
     * Add a move to the turn
     * @param Card $card Card object
     * @param Move $m    Move done
     */
    public function addCardMove(Card $card, Move $m)
    {
        $this->cards[] = $card;
        $this->moves[] = $m;
    }

    /**
     * Getter for cards used in the turn
     * @return array
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * Getter for moves done in the turn
     * @return array
     */
    public function getMoves()
    {
        return $this->moves;
    }

    /**
     * Sets guess type for the turn
     * @param int $type
     */
    public function setGuessType($type)
    {
        $this->guess_type = $type;
    }

    /**
     * Gets guess type of the turn
     * @return int
     */
    public function getGuessType()
    {
        return $this->guess_type;
    }

    /**
     * Sets match flag
     * @param boolean $match
     */
    public function setMatch($match)
    {
        return ($this->match = $match);
    }

    /**
     * Have cards been matched
     * @return boolean
     */
    public function isMatch()
    {
        return $this->match;
    }

    /**
     * Checks if turn has been completed
     * @return boolean
     */
    public function completed()
    {
        return count($this->cards) === $this->cards_per_turn;
    }

    /**
     * Gets player of the turn
     * @return PlayerInterface
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Gets player's id
     * @return string
     */
    public function getPlayerId()
    {
        return $this->player_id;
    }
}
