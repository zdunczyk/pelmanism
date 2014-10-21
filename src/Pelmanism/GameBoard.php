<?php

namespace Pelmanism;

use Pelmanism\Move\Move;
use Pelmanism\Deck\Deck;

/**
 * Main class for game board
 */
class GameBoard
{

    /**
     * Card deck
     * @var Deck
     */
    private $deck;

    /**
     * Width of the board
     * @var int
     */
    private $width;

    /**
     * Height of the board
     * @var int
     */
    private $height;

    /**
     * Returns linear position from 2d coords
     * @param  int $x X position on the board
     * @param  int $y Y position on the board
     * @return int Linear position on the board
     */
    private function getLinearPos($x, $y)
    {
        return $y * $this->getWidth() + $x;
    }

    /**
     * Creates new board from deck and dimensions
     * @param Deck   $deck   Deck of cards which will be laid on the board
     * @param int $width  Width of the board
     * @param int $height Height of the board
     */
    public function __construct(Deck $deck, $width, $height)
    {
        $this->deck = $deck;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Gets a card from specific position
     * @param  int $x X position
     * @param  int $y Y position
     * @return Card    Card object
     */
    public function getCardAt($x, $y)
    {
        return $this->getDeck()->getCardAt($this->getLinearPos($x, $y));
    }

    /**
     * Gets a card by game move
     * @param  Move   $m Game move which specifies position
     * @return Card   Card object 
     */
    public function getCardBy(Move $m)
    {
        return $this->getCardAt($m->getX(), $m->getY());
    }

    /**
     * Remove card from position
     * @param  int $x X position
     * @param  int $y Y position
     * @return null
     */
    public function removeCardAt($x, $y)
    {
        return $this->getDeck()->removeCard($this->getLinearPos($x, $y));
    }

    /**
     * Remove card by game move
     * @param  int $x X position
     * @param  int $y Y position
     * @return null
     */
    public function removeCardBy(Move $m)
    {
        return $this->removeCardAt($m->getX(), $m->getY());
    }

    /**
     * Checks if the board is empty
     * @return boolean Returns true when the board is empty
     */
    public function isEmpty()
    {
        return $this->deck->isEmpty();
    }

    /**
     * Returns deck laid on board
     * @return Deck Deck object
     */
    public function getDeck()
    {
        return $this->deck;
    }

    /**
     * Getter for board's width
     * @return int Width of board
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Getter for board's height
     * @return int Height of board
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Returns full playing board
     * @return string
     */
    public function __toString()
    {
        $result = '';

        for ($y = 0; $y < $this->getHeight(); $y++) {
            for ($x = 0; $x < $this->getWidth(); $x++) {
                $card = $this->getCardAt($x, $y);
                $result .= sprintf("%6s ", is_null($card) ? '' : $card->shortName());
            }
            $result .= "\n";
        }
        return $result;
    }
}
