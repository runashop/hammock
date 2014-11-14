<?php

class Commands extends SlackServicePlugin
{

    /**
     * @var string
     */
    public $name = "Slack Commands";

    /**
     * @var string
     */
    public $desc = "Execute commands from Slack";

    /**
     * @var array
     */
    public $cfg = [
        'has_token' => true,
    ];

}