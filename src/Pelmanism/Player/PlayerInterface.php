<?php

namespace Pelmanism\Player;

use Pelmanism\Card\Card;
use Pelmanism\Move\Move;
use Pelmanism\Move\MoveResolverInterface;

/**
 * Interface for game's players
 */
interface PlayerInterface
{

    /**
	 * Takes turn for the player
	 * @param  MoveResolverInterface $mr Game engine which knows the game rules
	 * @return null
	 */
    public function takeTurn(MoveResolverInterface $mr);

    /**
     * Event handler
     * @param  Card   $card Card which has been selected
     * @param  Move   $m    Move which has been made
     * @return null
     */
    public function cardPreviewed(Card $card, Move $m);

    /**
     * Event handler
     * @param  Card   $card Card which has been collected
     * @param  Move   $m    Collected card's position
     * @return null
     */
    public function cardCollected(Card $card, Move $m);
}
