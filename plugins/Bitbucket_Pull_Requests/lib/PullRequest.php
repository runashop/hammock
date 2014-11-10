<?php

namespace BPR;

class PullRequest
{

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var User[]
     */
    protected $reviewers = [];

    /**
     * @var User
     */
    protected $author;

    /**
     * @var User[]
     */
    protected $participants = [];

    /**
     * @var \DateTime
     */
    protected $updated;

    /**
     * @var \DateTime
     */
    protected $created;

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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

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
     * @return User[]
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param User[] $participants
     * @return $this
     */
    public function setParticipants($participants)
    {
        $this->participants = [];
        foreach ((array)$participants as $participant) {
            $this->addParticipant($participant);
        }
        return $this;
    }

    /**
     * @return User[]
     */
    public function getReviewers()
    {
        return $this->reviewers;
    }

    /**
     * @param User[] $reviewers
     * @return $this
     */
    public function setReviewers($reviewers)
    {
        $this->reviewers = [];
        foreach ((array)$reviewers as $reviewer) {
            $this->addReviewer($reviewer);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * @param array|User $participant
     * @return $this
     */
    public function addParticipant($participant)
    {
        if (!$participant instanceof User) {
            $participant = User::fromData($participant);
        }
        $this->participants[] = $participant;
        return $this;
    }

    /**
     * @param array|User $reviewer
     * @return $this
     */
    public function addReviewer($reviewer)
    {
        if (!$reviewer instanceof User) {
            $reviewer = User::fromData($reviewer);
        }
        $this->reviewers[] = $reviewer;
        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'author' => $this->getAuthor()->toArray(),
            'reviewers' => array_map(function (User $reviewer) {
                return $reviewer->toArray();
            }, $this->getReviewers()),
            'participants' => array_map(function (User $participant) {
                return $participant->toArray();
            }, $this->getParticipants()),
            'link' => $this->getLink(),
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated(),
        ];
    }

    public static function fromData($data)
    {
        if (!isset(
            $data['id'],
            $data['title'],
            $data['description'],
            $data['links']['html']['href'],
            $data['reviewers'],
            $data['participants'],
            $data['author'],
            $data['created_on'],
            $data['updated_on']
        )) {
            throw new \InvalidArgumentException('Wrong Pull-request data provided');
        }

        return (new self())
            ->setId($data['id'])
            ->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setAuthor($data['author'])
            ->setReviewers($data['reviewers'])
            ->setParticipants($data['[participants'])
            ->setLink($data['links']['html']['href'])
            ->setCreated($data['created_on'])
            ->setUpdated($data['updated_on']);
    }

} 