<?php

namespace Pelmanism\Deck;

use Pelmanism\Card\Card;
use Pelmanism\Card\GenericCard;

/**
 * Initializes a deck from config file
 */
class DeckBuilder
{

    /**
     * Returns deck object initialized from config file
     * @param  string $config_file Name of YAML config file
     * @return ShufflableDeck Initialized deck object
     */
    public static function prepareShufflable($config_file)
    {
        $config = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($config_file));
        $deck = new ShufflableDeck();

        foreach ($config['deck']['cards'] as $suits => $numbers) {
            foreach (explode(',', $suits) as $suit) {
                $suit = trim($suit);

                foreach ($numbers as $number) {
                    $card = null;

                    if (in_array($number, $config['deck']['generic_cards'])) {
                        $card = new GenericCard($suit, $number);
                    } else {
                        $card = new Card($suit, $number);
                    }

                    $deck->addCard($card);
                }
            }
        }

        return $deck;
    }
}
