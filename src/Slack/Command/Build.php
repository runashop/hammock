<?php

namespace Slack\Command;

use PhpSlackBot\Command\BaseCommand;
use Slack\Exception\BuildFailedException;
use Slack\Exception\ProjectNotFoundException;
use Slack\Exception\SlackException;
use Zend\Http\Client;
use Zend\Http\Request;

/**
 * Class Build
 * @package Slack\Command
 */
class Build extends BaseCommand
{

    const PHPCI_URL = 'http://phpci.runashop.net/webhook/bitbucket/%d';

    /**
     * @var array
     */
    protected static $projects = [
        '1' => 'pp2',
        '2' => 'common',
        '3' => 'profiler',
        '4' => 'finders',
    ];

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
        try {
            $command = trim(str_replace($this->getName(), '', $message['text']));
            $branch = $projectId = $commit = null;
            if ($command !== '') {
                list($project, $branch) = array_pad(explode(':', $command), 2, null);
                if (!($projectId = array_search($project, self::$projects, true))) {
                    throw new ProjectNotFoundException('Project ' . $project . ' not found');
                }
                if (strpos($branch, '/')) {
                    list($branch, $commit) = explode('/', $branch);
                }
            }
            if (!$branch) {
                $branch = 'master';
                $this->respond('As no branch was provided, master branch is used by default');
            }
            $author = 'Victor Gryshko <vgryshko@brightgrove.com>';
            $userId = $this->getCurrentUser();
            $users = $context['users'];
            if (is_array($users)) {
                foreach ((array)$users as $user) {
                    if (array_key_exists('profile', $user) && $user['id'] === $userId && is_array($user['profile'])) {
                        $author = sprintf('%s <%s>', $user['profile']['real_name_normalized'], $user['profile']['email']);
                        break;
                    }
                }
            }

            $result = $this->createBuild($projectId, $author, $branch, $commit);

            if (!isset($result['status']) || $result['status'] !== 'ok') {
                throw new BuildFailedException('Build running failed: ' . (isset($result['error']) ? $result['error'] : 'Unknown error'));
            }

            if (isset($result['commits']) && is_array($result['commits'])) {
                foreach ((array)$result['commits'] as $commit => $build) {
                    if (isset($build['buildID'])) {
                        $this->respond(sprintf('Build http://phpci.runashop.net/build/view/%d created', $build['buildID']));
                    } elseif (isset($build['message'])) {
                        $this->respond(sprintf('Build %s: %s', $build['status'], $build['message']));
                    }
                }
            }
        } catch (SlackException $e) {
            $this->respond('Error occurred: ' . $e->getMessage());
        }
    }

    /**
     * @param string $projectId
     * @param string $author
     * @param string$branch
     * @param string $commit
     * @return array
     */
    protected function createBuild($projectId, $author, $branch, $commit = null)
    {
        $bitbucketPayload = json_encode([
            'commits' => [
                [
                    'raw_author' => $author,
                    'raw_node' => $commit,
                    'branch' => $branch,
                    'message' => 'Manual build'
                ]
            ],
        ]);

        $client = new Client();
        $request = new Request();
        $request->setUri(sprintf(self::PHPCI_URL, $projectId));
        $request->setMethod(Request::METHOD_POST);
        $request->getPost()->fromArray([
            'payload' => $bitbucketPayload,
        ]);
        $client->setEncType(Client::ENC_FORMDATA);
        $response = $client->dispatch($request);

        return json_decode($response->getBody(), true);
    }

    /**
     * @param string $text
     */
    protected function respond($text)
    {
        $this->send($this->getCurrentChannel(), null, $text);
    }
}
