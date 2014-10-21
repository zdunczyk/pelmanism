<?php

namespace Pelmanism\Move;

/**
 * Class which represents a game move
 */
class Move
{

    /**
     * X deck position
     * @var int
     */
    private $x;

    /**
     * Y deck position
     * @var 
     */
    private $y;

    /**
     * Creates a new move (select of cards)
     * @param int $x X position
     * @param int $y Y position
     */
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Getter for X position
     * @return int X position
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Getter for Y position
     * @return int Y position
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Returns unique id for the move
     * @return string Unique id
     */
    public function getUniqueId()
    {
        return $this->getX() . '.' . $this->getY();
    }
}
