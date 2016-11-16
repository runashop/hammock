<?php

namespace Slack\Command;

use PhpSlackBot\Command\BaseCommand;

/**
 * Class Build
 * @package Slack\Command
 */
class Build extends BaseCommand
{

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('build');
    }

    /**
     * @param string $message
     * @param mixed $context
     */
    protected function execute($message, $context)
    {
        $this->send($this->getCurrentChannel(), null, 'Hello !' . $message);
    }
}
