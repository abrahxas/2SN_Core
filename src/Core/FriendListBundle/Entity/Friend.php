<?php

namespace Core\FriendListBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Friend
 *
 * @ORM\Table(name="friend")
 * @ORM\Entity(repositoryClass="Core\FriendListBundle\Entity\FriendRepository")
 */
class Friend
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
     * @ORM\ManyToOne(targetEntity="Core\FriendListBundle\Entity\FriendGroups", inversedBy="friend")
     * @ORM\JoinColumn(name="friendgroup_id", referencedColumnName="id", onDelete="CASCADE"))
     */
    private $friendgroup;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="friend")
     * @ORM\JoinColumn(name="friend_id", referencedColumnName="id", onDelete="CASCADE"))
     */
    private $friend;

    /**
     *@var collection 
     * @ORM\Column(name="sender", type="string", length=255)
     */
    private $sender;


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
     * Set friendgroup
     *
     * @param \Core\FriendListBundle\Entity\FriendGroups $friendgroup
     * @return Friend
     */
    public function setFriendgroup(\Core\FriendListBundle\Entity\FriendGroups $friendgroup = null)
    {
        $this->friendgroup = $friendgroup;

        return $this;
    }

    /**
     * Get friendgroup
     *
     * @return \Core\FriendListBundle\Entity\FriendGroups 
     */
    public function getFriendgroup()
    {
        return $this->friendgroup;
    }

    /**
     * Set friend
     *
     * @param \Core\UserBundle\Entity\User $friend
     * @return Friend
     */
    public function setFriend(\Core\UserBundle\Entity\User $friend = null)
    {
        $this->friend = $friend;

        return $this;
    }

    /**
     * Get friend
     *
     * @return \Core\UserBundle\Entity\User 
     */
    public function getFriend()
    {
        return $this->friend;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Friend
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
     * Set sender
     *
     * @param string $sender
     * @return Friend
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return string 
     */
    public function getSender()
    {
        return $this->sender;
    }
}
