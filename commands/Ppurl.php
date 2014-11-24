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