<?php

require_once __DIR__ . '/lib/User.php';
require_once __DIR__ . '/lib/PullRequest.php';

/**
 * Class Bitbucket_Pull_Requests
 *
 * @property Smarty $smarty
 */
class Bitbucket_Pull_Requests extends SlackServicePlugin
{

    /**
     * @var string
     */
    public $name = "Bitbucket PR";

    /**
     * @var string
     */
    public $desc = "Bitbucket Pull Requests Notifications";

    private $_handlers = [
        'pullrequest_merged' => 'onPullRequestMerged',
        'pullrequest_declined' => 'onPullRequestDeclined',
        'pullrequest_unapprove' => 'onPullRequestUnApproved',
        'pullrequest_created' => 'onPullRequestCreated',
        'pullrequest_updated' => 'onPullRequestUpdated',
        'pullrequest_comment_created' => 'onPullRequestComment',
        'pullrequest_approve' => 'onPullRequestApproved',
    ];

    /**
     * @var array
     */
    public $cfg = [
        'has_token' => true,
    ];

    public function onInit()
    {
        $channels = $this->getChannelsList();
        foreach ($channels as $k => $v) {
            if ($v == '#general') {
                $this->icfg['channel'] = $k;
                $this->icfg['channel_name'] = $v;
                break;
            }
        }
        $this->icfg['botname'] = 'Bitbucket PR';
    }

    public function onView()
    {
        $this->smarty->assign([
            'hook_url' => $this->getHookUrl(),
            'edit_url' => $this->getEditUrl(),
            'config' => $this->icfg,
        ]);
        return $this->smarty->fetch('view.tpl');
    }

    public function onEdit()
    {
        $channels = $this->getChannelsList();

        if (!empty($_POST) && isset($_POST['channel']) && array_key_exists($_POST['channel'], $channels)) {
            $this->icfg['channel'] = $_POST['channel'];
            $this->icfg['channel_name'] = $channels[$_POST['channel']];
            $this->icfg['botname'] = strip_tags($_POST['botname']);

            $this->saveConfig();

            header("location: {$this->getViewUrl()}&saved=1");
            exit;
        }

        $this->smarty->assign([
            'channels' => $channels,
            'edit_url' => $this->getEditUrl(),
            'config' => $this->icfg,
        ]);
        return $this->smarty->fetch('edit.tpl');
    }

    public function onHook($request)
    {
        if (!$this->icfg['channel']){
            return [
                'ok'	=> false,
                'error'	=> 'No channel configured',
            ];
        }

        try {
            $data = isset($request['post_body']) ? json_decode($request['post_body'], true) : null;

            if (!$data) {
                throw new RuntimeException('No data received');
            }

            foreach ($this->_handlers as $property => $handler) {
                if (array_key_exists($property, $data)) {
                    call_user_func([$this, $handler], $data[$property]);
                }
            }
        } catch (Exception $e) {
            return [
                'ok'	=> false,
                'error'	=> $e->getMessage(),
            ];
        }

        return [
            'status' => 'Bitbucket pull-request',
            'ok' => true,
        ];
    }

    public function getLabel()
    {
        return 'Bitpucket Pull-Requests will be posted to: ' . $this->icfg['channel_name'];
    }

    /**
     * @param string $text
     * @return array
     */
    private function sendMessage($text)
    {
        $this->postToChannel($text, array(
            'channel' => $this->icfg['channel'],
            'username' => $this->icfg['botname'],
        ));

        return [
            'ok' => true,
            'status' => 'Sent a message',
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    protected function onPullRequestCreated($data)
    {
        $this->smarty->assign('pr', \BPR\PullRequest::fromData($data)->toArray());
        return $this->sendMessage($this->smarty->fetch('pullrequest/created.tpl'));
    }

    /**
     * @param array $data
     * @return array
     */
    protected function onPullRequestUpdated($data)
    {
        //Bad bitbucket mapping fix
        $data['updated_on'] = new DateTime();
        $data['created_on'] = new DateTime();
        $data['reviewers'] = $data['participants'] = [];
        $data['link'] = '';
        $this->smarty->assign('pr', \BPR\PullRequest::fromData($data)->toArray());
        return $this->sendMessage($this->smarty->fetch('pullrequest/updated.tpl'));
    }

    /**
     * @param array $data
     * @return array
     */
    protected function onPullRequestMerged($data)
    {
        $this->smarty->assign('pr', \BPR\PullRequest::fromData($data)->toArray());
        return $this->sendMessage($this->smarty->fetch('pullrequest/merged.tpl'));
    }

    /**
     * @param array $data
     * @return array
     */
    protected function onPullRequestApproved($data)
    {
        $this->smarty->assign('user', \BPR\User::fromData($data['user'])->toArray());
        return $this->sendMessage($this->smarty->fetch('pullrequest/approved.tpl'));
    }

    /**
     * @param array $data
     * @return array
     */
    protected function onPullRequestUnApproved($data)
    {
        //Bad bitbucket mapping fix
        $data['user']['links']['avatar']['href'] = '';
        $this->smarty->assign('user', \BPR\User::fromData($data)->toArray());
        return $this->sendMessage($this->smarty->fetch('pullrequest/unapproved.tpl'));
    }

    /**
     * @param array $data
     * @return array
     */
    protected function onPullRequestDeclined($data)
    {
        $this->smarty->assign('pr', \BPR\PullRequest::fromData($data)->toArray());
        return $this->sendMessage($this->smarty->fetch('pullrequest/declined.tpl'));
    }

    /**
     * @param array $data
     * @return array
     */
    protected function onPullRequestComment($data)
    {
        $this->smarty->assign('comment', \BPR\Comment::fromData($data)->toArray());
        return $this->sendMessage($this->smarty->fetch('pullrequest/comment.tpl'));
    }

} 