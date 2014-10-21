<?php

namespace Pelmanism\Card;

/**
 * Generic card is a special type of game card which can be declared
 */
class GenericCard extends Card
{

    /**
     * Declared number on card
     * @var string|null
     */
    static private $declared = null;

    /**
     * {@inhritDoc}
     */
    public function declareNumber($number)
    {
        if (is_null(self::$declared)) {
            self::$declared = $number;
        } else {
            throw new \Exception('Other GenericCard has been declarated!');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getNumber()
    {
        if (is_null(self::$declared)) {
            return parent::getNumber();
        }

        return (string)(self::$declared);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString()
    {
        return $this->number . ' ' . $this->getSuit()
                . (!is_null(self::$declared) ? '(declared as ' . self::$declared . ')' : '');
    }
}
