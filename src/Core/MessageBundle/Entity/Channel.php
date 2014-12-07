<?php

namespace Core\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Core\GameSessionBundle\Entity\GameSession;

/**
 * Channel
 *
 * @ORM\Table(name="channel")
 * @ORM\Entity(repositoryClass="Core\MessageBundle\Entity\ChannelRepository")
 */
class Channel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Core\UserBundle\Entity\User", inversedBy="channels")
     * @ORM\JoinColumn(name="users_id", referencedColumnName="id", onDelete="CASCADE"))
     */
    private $users;

    /**
     *
     * @ORM\OneToMany(targetEntity="Message", mappedBy="channel", cascade={"persist"})
     */
    private $messages;

    /**
     * @ORM\ManyToOne(targetEntity="Core\GameSessionBundle\Entity\GameSession", inversedBy="channels")
     * @ORM\JoinColumn(name="game_session_id", referencedColumnName="id", onDelete="CASCADE"))
     */
    private $gameSession;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param  string  $name
     * @return Channel
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add users
     *
     * @param  \Core\UserBundle\Entity\User $users
     * @return Channel
     */
    public function addUser(\Core\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Core\UserBundle\Entity\User $users
     */
    public function removeUser(\Core\UserBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add messages
     *
     * @param  \Core\MessageBundle\Entity\Message $messages
     * @return Channel
     */
    public function addMessage(\Core\MessageBundle\Entity\Message $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \Core\MessageBundle\Entity\Message $messages
     */
    public function removeMessage(\Core\MessageBundle\Entity\Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set gameSession
     *
     * @param  \Core\GameSessionBundle\Entity\GameSession $gameSession
     * @return Channel
     */
    public function setGameSession(\Core\GameSessionBundle\Entity\GameSession $gameSession = null)
    {
        $this->gameSession = $gameSession;

        return $this;
    }

    /**
     * Get gameSession
     *
     * @return \Core\GameSessionBundle\Entity\GameSession
     */
    public function getGameSession()
    {
        return $this->gameSession;
    }
}
