<?php

require_once __DIR__ . '/vendor/autoload.php';

use PhpSlackBot\Bot;

$config = [
    'token' => 'xoxp-2760649163-2762475502-2955876209-14c3a7',
    'team' => 'Runashop',
    'username' => 'Runashop',
    'icon' => 'ICON', // Auto detects if it's an icon_url or icon_emoji
    'parse' => '',
];

$bot = new Bot();
$bot->setToken('rfE2xIVrYWsvbZmPTOB911yg'); // Get your token here https://my.slack.com/services/new/bot
$bot->loadCommand(new \Slack\Command\Build());
$bot->run();
