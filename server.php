<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpSlackBot\Bot;

$bot = new Bot();
$bot->setToken('xoxp-2760649163-2762475502-2955876209-14c3a7');
$bot->loadCommand(new \Slack\Command\Build());
$bot->run();
