<?php

namespace Pelmanism\Deck;

/**
 * Special deck which can be shuffled
 */
class ShufflableDeck extends Deck implements ShufflableInterface
{

    /**
	 * Shuffles the deck
	 * @return null
	 */
    public function shuffle()
    {
        /* @todo simulate overhand shuffle
         * @see http://www.youtube.com/watch?v=VkE8fNFBUw8
         */
        shuffle($this->cards);
    }
}
