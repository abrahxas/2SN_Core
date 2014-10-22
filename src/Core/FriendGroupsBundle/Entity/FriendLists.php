<?php

namespace Core\FriendGroupsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FriendLists
 *
 * @ORM\Table(name="friend_lists")
 * @ORM\Entity
 */
class FriendLists
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
     * @var integer
     *
     * @ORM\Column(name="users_id", type="integer", unique=true)
     */
    private $usersId;

    public function __construct()
    {
        $this->$usersId = 0;
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
     * Set usersId
     *
     * @param integer $usersId
     * @return FriendLists
     */
    public function setUsersId($usersId)
    {
        $this->usersId = $usersId;

        return $this;
    }

    /**
     * Get usersId
     *
     * @return integer 
     */
    public function getUsersId()
    {
        return $this->usersId;
    }
}
