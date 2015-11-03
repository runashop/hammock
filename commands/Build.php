<?php

namespace Slack\Command;

/**
 * Class Build
 * @package Slack\Command
 */
class Build extends AbstractCommand implements CommandInterface
{

    const PHPCI_URL = 'http://phpci.runashop.net/webhook/bitbucket/1';

    public function run($command)
    {
        list($branch, $commit) = array_pad(preg_split('/\s+/', $command), 2, '');
        if (!$branch) {
            $branch = 'dev';
        }
        $bitbucketPayload = json_encode([
            'commits' => [
                [
                    'raw_author' => 'Victor Gryshko <vgryshko@brightgrove.com>',
                    'raw_node' => $commit,
                    'branch' => $branch,
                    'message' => 'Manual build'
                ]
            ],
        ]);

        try {
            $request = $this->_client->client->post(self::PHPCI_URL, null, [
                'payload' => $bitbucketPayload,
            ]);
            $response = $request->send();

            $result = json_decode($response->getBody(), true);

            if (!isset($result['status']) || $result['status'] !== 'ok') {
                throw new \RuntimeException('Build running failed: ' . (isset($result['error']) ? $result['error'] : 'Unknown error'));
            }

            if (isset($result['commits'])) {
                foreach ($result['commits'] as $commit => $build) {
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