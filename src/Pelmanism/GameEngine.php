<?php

namespace Pelmanism;

use Pelmanism\Player\PlayerInterface;
use Pelmanism\Player\AutoPlayer;

use Pelmanism\Move\Move;
use Pelmanism\Move\MoveResolverInterface;

use Pelmanism\Card\Card;

/**
 * Main class which enforces game rules
 */
class GameEngine implements MoveResolverInterface
{

    /**
     * How many cards you can select per turn
     */
    const CARDS_PER_TURN = 2;

    /**
     * Array of registered players
     * @var array
     */
    private $players = array();

    /**
     * Board used for game
     * @var Board
     */
    private $board;

    /**
     * Current turn taken by one of players
     * @var Turn
     */
    private $current_turn;

    /**
     * Contains all forbidden moves for current game
     * @var array
     */
    private $forbidden_moves = array();

    /**
     * Self instance
     * @var GameEngine
     */
    private static $instance;


    /**
     * Clears forbidden array
     * @return null
     */
    private function clearForbidden()
    {
        $this->forbidden_moves = array();
    }

    /**
     * Forbids some move
     * @param  Move   $m Move which should to be forbidden
     * @return null
     */
    private function forbid(Move $m)
    {
        $this->forbidden_moves[] = $m->getUniqueId();
    }

    /**
     * Creates new game engine
     * @param string $config_file Name of YAML config file
     */
    private function __construct($config_file)
    {
        $config = \Symfony\Component\Yaml\Yaml::parse(file_get_contents($config_file));

        foreach ($config['engine']['players'] as $name) {
            $this->registerPlayer(new AutoPlayer($name));
        }

        if (count($this->players) < 2) {
            throw new \Exception('Register some players!');
        }

        $this->current_turn = $this->newTurn(0);
    }

    /**
     * Simple helper which calls passed function on each registered player
     * @param  function $callback Callback function
     * @return null
     */
    private function foreachPlayer($callback)
    {
        foreach ($this->players as $player) {
            $callback($player);
        }
    }

    /**
     * Notify all players about card select
     * @param  Card   $card Selected card
     * @param  Move   $m    Move made to select the card
     * @return null
     */
    private function cardPreviewed(Card $card, Move $m)
    {
        $this->foreachPlayer(function ($player) use ($card, $m) {
            $player->cardPreviewed($card, $m);
        });
    }

    /**
     * Notify all players about card collection
     * @param  Card   $card Card collected
     * @param  Move   $m    Move made to collect the card
     * @return null
     */
    private function cardCollected(Card $card, Move $m)
    {
        $this->board->removeCardAt($m->getX(), $m->getY());

        $this->foreachPlayer(function ($player) use ($card, $m) {
            $player->cardCollected($card, $m);
        });
    }

    /**
     * Begins the next turn
     * @return 
     */
    private function nextTurn()
    {
        $matching = false;

        if ($this->current_turn->completed()) {
            $cards = $this->current_turn->getCards();
            $moves = $this->current_turn->getMoves();

            $ref_number = reset($cards)->getNumber();
            $matching = true;

            foreach ($cards as $card) {
                $matching = $matching && ($ref_number === $card->getNumber());
            }

            if ($this->current_turn->setMatch($matching)) {
                $this->current_turn->getPlayer()->pairFound($cards[0], $cards[1]);

                foreach ($cards as $key => $card) {
                    $this->cardCollected($card, $moves[$key]);
                }
            }

            $this->current_turn = $this->newTurn($this->current_turn->getPlayerId() + 1);
            $this->clearForbidden();

            return $matching;
        }

        return $matching;
    }

    /**
     * Tries to find valid move nearest to the one passed
     * @param  Move   $m Reference move
     * @return Nearest move possible
     */
    private function findNearestValid(Move $m)
    {
        if ($this->board->isEmpty()) {
            throw new Exception('Game ended during the move');
        }

        $simple_check = $this->board->getCardBy($m);

        if (!is_null($simple_check) && !$this->isForbidden($m->getUniqueId())) {
            return $m;
        }

        $board_width = $this->board->getWidth();
        $board_height = $this->board->getHeight();

        for ($y = 0; $y < $board_height; $y++) {
            $ym = ($m->getY() + $y) % $board_height;
            for ($x = 0; $x < $board_width; $x++) {
                $xm = ($m->getX() + $x) % $board_width;

                if (!is_null($this->board->getCardAt($xm, $ym))) {
                    $move = new Move($xm, $ym);

                    if (!$this->isForbidden($move->getUniqueId())) {
                        return $move;
                    }
                }
            }
        }
    }

    /**
     * Registers player observer
     * @param  PlayerInterface $p Player
     * @return null
     */
    public function registerPlayer(PlayerInterface $p)
    {
        $this->players[] = $p;
    }

    /**
     * Does a move
     * @param  Move    $m         Object describing the move
     * @param  boolean $set_guess If true sets guess type for current Turn
     * @return Returns card selected in move
     */
    public function doMove(Move $m, $set_guess = true)
    {
        $valid_move = $this->findNearestValid($m);

        $card = $this->board->getCardBy($valid_move);
        $this->cardPreviewed($card, $valid_move);

        $this->current_turn->addCardMove($card, $valid_move);

        $this->forbid($valid_move);

        if ($set_guess) {
            if ($valid_move->getUniqueId() === $m->getUniqueId()) {
                $this->current_turn->setGuessType(Turn::REMEMBERED_CARD);
            } else {
                $this->current_turn->setGuessType(Turn::RANDOM_GUESS);
            }
        }

        return $card;
    }

    /**
     * Does a random move which is allowed
     * @return array Array of Card and Move
     */
    public function doRandomMove()
    {
        $move = new Move(
            rand(0, $this->board->getWidth() - 1),
            rand(0, $this->board->getHeight() - 1)
        );

        $this->current_turn->setGuessType(Turn::RANDOM_GUESS);

        return array($this->doMove($move, false), $move);
    }

    /**
     * Takes next turn
     * @return Returns current turn object
     */
    public function takeTurn()
    {
        $current = $this->current_turn;
        $this->current_turn->getPlayer()->takeTurn($this);

        $current->setMatch($this->nextTurn());
        return $current;
    }

    /**
     * Returns winner of the game
     * @return PlayerInterface|false The winner
     */
    public function getWinner()
    {
        if ($this->board->isEmpty()) {
            $best_player = null;
            $is_tie = false;

            $this->foreachPlayer(function ($player) use (&$best_player, &$is_tie) {
                if (empty($best_player) || $player->getResult() > $best_player->getResult()) {
                    $best_player = $player;
                    $is_tie = false;
                } else if ($player->getResult() === $best_player->getResult()) {
                    $is_tie = true;
                }
            });

            if ($is_tie) {
                return null;
            }

            return $best_player;
        }

        return false;
    }

    /**
     * Getter for players array
     * @return array Array of registered players
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Sets a board
     * @param GameBoard $gb Game board
     */
    public function setBoard(GameBoard $gb)
    {
        $this->board = $gb;
    }

    /**
     * Returns single instance of the class
     * @param  string $config Name of YAML config file
     * @return GameEngine
     */
    public static function getInstance($config)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

    /**
     * Checks if move is forbidden
     * @param  string  $move_id Unique move id
     * @return boolean          Returns true if move is forbidden
     */
    public function isForbidden($move_id)
    {
        return in_array($move_id, $this->forbidden_moves);
    }

    /**
     * Creates new turn object for the player
     * @param  int $player_idx Index of player in player set
     * @return Turn    New turn object
     */
    public function newTurn($player_idx)
    {
        $idx_norm = $player_idx % count($this->players);

        return new Turn(self::CARDS_PER_TURN, $this->players[$idx_norm], $idx_norm);
    }
}
