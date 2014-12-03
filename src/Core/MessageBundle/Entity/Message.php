<?php

namespace Core\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name = "message")
 * @ORM\Entity(repositoryClass="Core\MessageBundle\Entity\MessageRepository")
 */
class Message
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
     * @ORM\ManyToOne(targetEntity="Core\MessageBundle\Entity\Channel", inversedBy="messages")
     * @ORM\JoinColumn(name="channel_id", referencedColumnName="id", onDelete="CASCADE"))
     */
    private $channel;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="sender")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id", onDelete="CASCADE"))
     */
    private $sender;

    /**
     * @var string
     *
     * @ORM\Column(name="contents", type="string", length=255)
     */
    private $contents;


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
     * Set contents
     *
     * @param string $contents
     * @return Message
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * Get contents
     *
     * @return string 
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * Set channel
     *
     * @param \Core\MessageBundle\Entity\Channel $channel
     * @return Message
     */
    public function setChannel(\Core\MessageBundle\Entity\Channel $channel = null)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return \Core\MessageBundle\Entity\Channel 
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set sender
     *
     * @param \Core\UserBundle\Entity\User $sender
     * @return Message
     */
    public function setSender(\Core\UserBundle\Entity\User $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \Core\UserBundle\Entity\User 
     */
    public function getSender()
    {
        return $this->sender;
    }
}
