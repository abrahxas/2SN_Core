<?php

namespace Core\FriendListBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * friendGroups
 *
 * @ORM\Table(name="friendGroups")
 * @ORM\Entity
 */
class FriendGroups
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
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="friendGroups")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id"))
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Core\FriendListBundle\Entity\Friend" ,mappedBy="friend")
     */
    protected $friends;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * @param string $name
     * @return FriendGroups
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
     * Set user
     *
     * @param \Core\UserBundle\Entity\User $user
     * @return FriendGroups
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
     * Add friends
     *
     * @param \Core\FriendListBundle\Entity\Friend $friends
     * @return FriendGroups
     */
    public function addFriend(\Core\FriendListBundle\Entity\Friend $friends)
    {
        $this->friends[] = $friends;

        return $this;
    }

    /**
     * Remove friends
     *
     * @param \Core\FriendListBundle\Entity\Friend $friends
     */
    public function removeFriend(\Core\FriendListBundle\Entity\Friend $friends)
    {
        $this->friends->removeElement($friends);
    }

    /**
     * Get friends
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFriends()
    {
        return $this->friends;
    }
}
