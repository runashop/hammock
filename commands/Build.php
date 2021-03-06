<?php

namespace Slack\Command;

use ThreadMeUp\Slack\User;

/**
 * Class Build
 * @package Slack\Command
 */
class Build extends AbstractCommand
{

    const PHPCI_URL = 'http://phpci.runashop.net/webhook/bitbucket/%d';

    /**
     * @param string $command
     * @return void
     */
    public function run($command)
    {
        list($branch, $commit) = array_pad(preg_split('/\s+/', $command), 2, '');
        if (!$branch) {
            $branch = 'dev';
        }

        $projectId = 1;
        if (strpos($branch, ':') !== false) {
            list($branch, $projectId) = explode(':', $branch);
        }

        $userId = $this->_message->userId();
        $author = 'Victor Gryshko <vgryshko@brightgrove.com>';
        /** @var User[] $users */
        $users = $this->_client->users();
        foreach ($users as $user) {
            if ($user->id() === $userId) {
                $author = sprintf('%s <%s>', $user->name(), $user->email());
            }
        }

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

        try {
            $request = $this->_client->client->post(sprintf(self::PHPCI_URL, $projectId), null, [
                'payload' => $bitbucketPayload,
            ]);
            $response = $request->send();

            $result = json_decode($response->getBody(), true);

            if (!isset($result['status']) || $result['status'] !== 'ok') {
                throw new \RuntimeException('Build running failed: ' . (isset($result['error']) ? $result['error'] : 'Unknown error'));
            }

            if (isset($result['commits']) && is_array($result['commits'])) {
                foreach ((array)$result['commits'] as $commit => $build) {
                    if (isset($build['buildID'])) {
                        $this->_message->respond(sprintf('Build <http://phpci.runashop.net/build/view/%d|#%1$d> created', $build['buildID']));
                    } elseif (isset($build['message'])) {
                        $this->_message->respond(sprintf('Build %s: %s', $build['status'], $build['message']));
                    }
                }
            }
        } catch (\Exception $e) {
            $this->_message->respond('Exception: ' . $e->getMessage());
        }
    }
}
