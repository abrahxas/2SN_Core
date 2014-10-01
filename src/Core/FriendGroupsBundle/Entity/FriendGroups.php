<?php

namespace Core\FriendGroupsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FriendGroups
 *
 * @ORM\Table(name="friend_groups")
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
     * @var integer
     *
     * @ORM\Column(name="friendlists_id", type="integer")
     */
    private $friendlistsId;

    /**
     * @var integer
     *
     * @ORM\Column(name="users_id", type="integer")
     */
    private $usersId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="users_validation", type="boolean")
     */
    private $usersValidation;

    /**
     * @var string
     *
     * @ORM\Column(name="groups_names", type="string", length=255)
     */
    private $groupsNames;

    public function __construct()
    {
        $this->$friendlistsId = 0;
        $this->$usersId = 0;
        $this->$usersValidation = false;
        $this->$groupsNames = "general";
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
     * Set friendlistsId
     *
     * @param integer $friendlistsId
     * @return FriendGroups
     */
    public function setFriendlistsId($friendlistsId)
    {
        $this->friendlistsId = $friendlistsId;

        return $this;
    }

    /**
     * Get friendlistsId
     *
     * @return integer 
     */
    public function getFriendlistsId()
    {
        return $this->friendlistsId;
    }

    /**
     * Set usersId
     *
     * @param integer $usersId
     * @return FriendGroups
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

    /**
     * Set usersValidation
     *
     * @param boolean $usersValidation
     * @return FriendGroups
     */
    public function setUsersValidation($usersValidation)
    {
        $this->usersValidation = $usersValidation;

        return $this;
    }

    /**
     * Get usersValidation
     *
     * @return boolean 
     */
    public function getUsersValidation()
    {
        return $this->usersValidation;
    }

    /**
     * Set groupsNames
     *
     * @param string $groupsNames
     * @return FriendGroups
     */
    public function setGroupsNames($groupsNames)
    {
        $this->groupsNames = $groupsNames;

        return $this;
    }

    /**
     * Get groupsNames
     *
     * @return string 
     */
    public function getGroupsNames()
    {
        return $this->groupsNames;
    }
}
