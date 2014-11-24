<?php

namespace Slack\Command;

class Ppurl extends AbstractCommand implements CommandInterface
{

    /**
     * @param string $command
     * @return mixed
     */
    public function run($command)
    {
        $command = trim($command);
        if ($command[0] === '/') {
            $command = 'http://domain.com' . $command;
        }
        $query = parse_url($command, PHP_URL_QUERY);
        if ($query) {
            parse_str($query, $query);
            if (isset($query['url'])) {
                $command = $query['url'];
                $redirectUrl = str_rot13(implode('', (array)$command));
                $query = parse_url($redirectUrl, PHP_URL_QUERY);
                if ($query) {
                    parse_str($query, $redirectUrlData);
                    if (isset($redirectUrlData['u'])) {
                        $redirectUrl = $redirectUrlData['u'];
                        $redirectUrlData = json_decode(base64_decode(rawurldecode($redirectUrl)), true);
                        if (isset($redirectUrlData['url'])) {
                            $redirectUrl = $redirectUrlData['url'];
                            $this->_message->respond($redirectUrl);
                        }
                    }
                }
            }
        }
    }

} 