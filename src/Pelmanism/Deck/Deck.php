<?php

namespace Pelmanism\Deck;

use Pelmanism\Card\Card;

/**
 * Main class which represents a game deck
 */
class Deck
{

    /**
     * Set of card objects
     * @var Card
     */
    protected $cards;

    /**
     * Adds a new card to the deck
     * @param Card $c New card to be added
     */
    public function addCard(Card $c)
    {
        $this->cards[] = $c;
    }

    /**
     * Takes one card from specific position off the deck 
     * @param  int $position Integer value which indicates card position
     * @return null
     */
    public function removeCard($position)
    {
        $this->cards[$position] = null;
    }

    /**
     * Returns card at specific position
     * @param  int $position Integer value which indicates card position
     * @return Card Card object from defined position
     */
    public function getCardAt($position)
    {
        if ($position < $this->getSize()) {
            if (isset($this->cards[$position])) {
                return $this->cards[$position];
            }

            return null;
        }
    }

    /**
     * Checks if deck is empty
     * @return boolean True if deck is empty
     */
    public function isEmpty()
    {
        $result = true;

        foreach ($this->cards as $card) {
            $result = $result && is_null($card);
        }

        return $result;
    }

    /**
     * Returns size of the deck
     * @return int Size of the deck
     */
    public function getSize()
    {
        return count($this->cards);
    }

    /**
     * Returns stringified list of cards
     * @return string
     */
    public function __toString()
    {
        $result = '';
        foreach ($this->cards as $card) {
            $result .= $card . "\n";
        }

        return $result;
    }
}
