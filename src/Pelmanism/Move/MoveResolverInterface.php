<?php

namespace Pelmanism\Move;

/**
 * Interface for object which can do moves which follow the rules of game
 */
interface MoveResolverInterface
{

    /**
	 * Do move specified by passed object 
	 * @param  Move   $m Card select representation
	 * @return Card Returns card selected by move
	 */
    public function doMove(Move $m);

    /**
     * Do some random allowed move
     * @return Card Returns card selected by move
     */
    public function doRandomMove();

    /**
     * Checks if move has been marked as forbidden
     * @param  string  $move_id Move's unique id
     * @return boolean          Returns true if move identified by $move_id is forbidden
     */
    public function isForbidden($move_id);
}
