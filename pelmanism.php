<?php

include_once 'vendor/autoload.php';

use Pelmanism\Deck\DeckBuilder;
use Pelmanism\GameBoard;
use Pelmanism\GameEngine;
use Pelmanism\Turn;

const CONFIG_FILE = 'config.yml';

const GAME_BOARD_WIDTH = 6;
const GAME_BOARD_HEIGHT = 9;

print "Preparing the deck.\n";
$deck = DeckBuilder::prepareShufflable(CONFIG_FILE);

print "Shuffling...\n";
$deck->shuffle();

print "Laying out the cards\n";
$board = new GameBoard($deck, GAME_BOARD_WIDTH, GAME_BOARD_HEIGHT);

print $board . "\n";

print "Let the game begin!\n\n";

$engine = GameEngine::getInstance(CONFIG_FILE);
$engine->setBoard($board);

while(($winner = $engine->getWinner()) === false) {
    $t = $engine->takeTurn();
    $current_player = $t->getPlayer();
    $cards = $t->getCards();

    print "It's $current_player's turn\n";
    print "$current_player finds " . $cards[0] . "\n";

    $remembers = ($t->getGuessType() === Turn::REMEMBERED_CARD);

    print "$current_player " . ($remembers ? 'remembers' : 'finds') . ' ' . $cards[1] . '... ' . ($t->isMatch() ? 'Match!' : 'No match') . "\n";

    print "$current_player has " . $current_player->getResult() . "\n\n";   

    print $board . "\n";

    //fgets(STDIN); // step by step
}

print "Game over.\n";

foreach($engine->getPlayers() as $player)
    print "$player has " . $player->getResult() . " pairs.\n";

if(is_null($winner))
    print "\nGame ended in a tie\n";
else
    print "\n$winner is the winner!\n";