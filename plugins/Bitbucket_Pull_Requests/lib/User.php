<?php

namespace BPR;

class User
{

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $displayName;

    /**
     * @var string
     */
    protected $avatar;

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     * @return $this
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return $this
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'username' => $this->getUsername(),
            'display_name' => $this->getDisplayName(),
            'avatar' => $this->getAvatar(),
        ];
    }

    /**
     * @param $data
     * @return $this
     */
    public static function fromData($data)
    {
        if (!isset(
            $data['username'],
            $data['display_name'],
            $data['links']['avatar']['href']
        )) {
            throw new \InvalidArgumentException('Invalid data format');
        }

        return (new self())
            ->setUsername($data['username'])
            ->setDisplayName($data['display_name'])
            ->setAvatar($data['links']['avatar']['href']);
    }

} 