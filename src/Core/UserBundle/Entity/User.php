<?php

namespace Core\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Core\FriendListBundle\Entity\FriendGroups;
// use FOS\MessageBundle\Model\ParticipantInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth_date", type="datetime", nullable=true)
     */
    private $birthDate = null;

    /**
     *@var friendGroups[]
     * @ORM\OneToMany(targetEntity="Core\FriendListBundle\Entity\FriendGroups", mappedBy="user", cascade="persist")
     */
    protected $friendGroups;

    /**
     * @ORM\OneToMany(targetEntity="Core\BlogBundle\Entity\Post", mappedBy="user")
     */
    protected $posts;

    /**
     * @ORM\OneToMany(targetEntity="Core\GalleryBundle\Entity\Album", mappedBy="user")
     */
    protected $albums;


    /**
     * Constructor
     */
    public function __construct()
    {
//        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct();

        $this->friendGroups = new \Doctrine\Common\Collections\ArrayCollection();

        $generalGroup = new FriendGroups();
        $generalGroup->setUser($this);
        $generalGroup->setName('general');
        
        $this->addFriendGroup($generalGroup);

        $waitGroup = new FriendGroups();
        $waitGroup->setUser($this);
        $waitGroup->setName('wait');
        
        $this->addFriendGroup($waitGroup);
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
     * Set birthDate
     *
     * @param \DateTime $birthDate
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Add posts
     *
     * @param \Core\BlogBundle\Entity\Post $posts
     * @return User
     */
    public function addPost(\Core\BlogBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \Core\BlogBundle\Entity\Post $posts
     */
    public function removePost(\Core\BlogBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Add albums
     *
     * @param \Core\GalleryBundle\Entity\Album $albums
     * @return User
     */
    public function addAlbum(\Core\GalleryBundle\Entity\Album $albums)
    {
        $this->albums[] = $albums;

        return $this;
    }

    /**
     * Remove albums
     *
     * @param \Core\GalleryBundle\Entity\Album $albums
     */
    public function removeAlbum(\Core\GalleryBundle\Entity\Album $albums)
    {
        $this->albums->removeElement($albums);
    }

    /**
     * Get albums
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * Add friendGroups
     *
     * @param \Core\FriendListBundle\Entity\FriendGroups $friendGroups
     * @return User
     */
    public function addFriendGroup(\Core\FriendListBundle\Entity\FriendGroups $friendGroups)
    {
        $this->friendGroups[] = $friendGroups;

        return $this;
    }

    /**
     * Remove friendGroups
     *
     * @param \Core\FriendListBundle\Entity\FriendGroups $friendGroups
     */
    public function removeFriendGroup(\Core\FriendListBundle\Entity\FriendGroups $friendGroups)
    {
        $this->friendGroups->removeElement($friendGroups);
    }

    /**
     * Get friendGroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFriendGroups()
    {
        return $this->friendGroups;
    }
}
