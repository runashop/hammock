<?php

namespace BPR;

class Comment
{

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var User
     */
    protected $author;

    /**
     * @var \DateTime
     */
    protected $updated;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User|array $author
     * @return $this
     */
    public function setAuthor($author)
    {
        if (!$author instanceof User) {
            $author = User::fromData($author);
        }
        $this->author = $author;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     * @return $this
     */
    public function setCreated($created)
    {
        if (!$created instanceof \DateTime) {
            $created = new \DateTime($created);
        }
        $this->created = $created;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     * @return $this
     */
    public function setUpdated($updated)
    {
        if (!$updated instanceof \DateTime) {
            $updated = new \DateTime($updated);
        }
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'text' => $this->getText(),
            'link' => $this->getLink(),
            'author' => $this->getAuthor()->toArray(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated(),
        ];
    }

    /**
     * @param $data
     * @return $this
     */
    public static function fromData($data)
    {
        if (!isset(
            $data['id'],
            $data['content']['raw'],
            $data['links']['html']['href'],
            $data['user'],
            $data['created_on'],
            $data['updated_on']
        )) {
            throw new \InvalidArgumentException('Wrong Pull-request comment data provided');
        }

        return (new self())
            ->setId($data['id'])
            ->setText($data['content']['raw'])
            ->setAuthor($data['user'])
            ->setLink($data['links']['html']['href'])
            ->setCreated($data['created_on'])
            ->setUpdated($data['updated_on']);
    }

} 