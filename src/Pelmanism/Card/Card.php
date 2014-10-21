<?php

namespace Pelmanism\Card;

/**
 * Class for game cards
 */
class Card
{

    /**
     * Name of suit
     */
    protected $suit;

    /**
     * Number on card
     */
    protected $number;

    /**
     * Creates new card object with specified suit and number
     * @param string $suit   The name of suit
     * @param string $number The number on card
     */
    public function __construct($suit, $number)
    {
        $this->suit = $suit;
        $this->number = $number;
    }

    /**
     * Declares the card with passed number
     * @param  string $number Number for the card to be declared with
     * @return null
     */
    public function declareNumber($number)
    {
        throw new \Exception("Cannot declare $this card!");
    }

    /**
     * Getter for suit param
     * @return string Card's suit
     */
    public function getSuit()
    {
        return $this->suit;
    }

    /**
     * Getter for number on card
     * @return string Card's number
     */
    public function getNumber()
    {
        return (string)$this->number;
    }

    /**
     * @return string String representation of the Card object
     */
    public function __toString()
    {
        return $this->getNumber() . ' ' . $this->getSuit();
    }

    /**
     * Returns short name of card suitable for deck field
     * @return string Short representation of the Card object
     */
    public function shortName()
    {
        return $this->getSuit()[0] . $this->getNumber();
    }
}
