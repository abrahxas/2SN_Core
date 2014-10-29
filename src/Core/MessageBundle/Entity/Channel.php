<?php

namespace Core\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Channel
 *
 * @ORM\Table()
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
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="channel")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id"))
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Core\ChatroomBundle\Entity\Chatroom", inversedBy="channel")
     * @ORM\JoinColumn(name="chatroom_id", referencedColumnName="id"))
     */
    private $chatroom;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="channel")
     * @ORM\JoinColumn(name="participant_id", referencedColumnName="id"))
     */
    private $participants;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="channel", cascade={"remove"})
     */
    private $messages;

   
    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set user
     *
     * @param \Core\UserBundle\Entity\User $user
     * @return Channel
     */
    public function setUser(\Core\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Core\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set chatroom
     *
     * @param \Core\ChatroomBundle\Entity\Chatroom $chatroom
     * @return Channel
     */
    public function setChatroom(\Core\ChatroomBundle\Entity\Chatroom $chatroom = null)
    {
        $this->chatroom = $chatroom;

        return $this;
    }

    /**
     * Get chatroom
     *
     * @return \Core\ChatroomBundle\Entity\Chatroom 
     */
    public function getChatroom()
    {
        return $this->chatroom;
    }

    /**
     * Set participants
     *
     * @param \Core\UserBundle\Entity\User $participants
     * @return Channel
     */
    public function setParticipants(\Core\UserBundle\Entity\User $participants = null)
    {
        $this->participants = $participants;

        return $this;
    }

    /**
     * Get participants
     *
     * @return \Core\UserBundle\Entity\User 
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * Add messages
     *
     * @param \Core\MessageBundle\Entity\Message $messages
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
}
