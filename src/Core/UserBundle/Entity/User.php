<?php

namespace Core\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Core\FriendListBundle\Entity\FriendGroups;
use Symfony\Component\HttpFoundation\File\File;

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
     * @var string
     *
     * @ORM\Column(name="image_profile", type="string", nullable=true)
     */
    private $imageProfile;

    /**
     *@var friendGroups[]
     * @ORM\OneToMany(targetEntity="Core\FriendListBundle\Entity\FriendGroups", mappedBy="user", cascade="persist")
     */
    protected $friendGroups;

    /**
     * @var \Core\BlogBundle\Entity\Post[]
     * @ORM\OneToMany(targetEntity="Core\BlogBundle\Entity\Post", mappedBy="user", cascade={"persist"})
     */
    protected $posts;

    /**
     * @var \Core\GalleryBundle\Entity\Album[]
     * @ORM\OneToMany(targetEntity="Core\GalleryBundle\Entity\Album", mappedBy="user", cascade={"persist"})
     */
    protected $albums;

    /**
     * @var \Core\CharacterSheetBundle\Entity\CharacterSheet[]
     * @ORM\OneToMany(targetEntity="Core\CharacterSheetBundle\Entity\CharacterSheet", mappedBy="user", cascade={"persist"})
     */
    protected $characterSheets;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->albums = new \Doctrine\Common\Collections\ArrayCollection();
        $albumWall = new \Core\GalleryBundle\Entity\Album();
        $albumWall->setName('Wall');
        $albumWall->setUser($this);
        $this->addAlbum($albumWall);

        $albumProfile = new \Core\GalleryBundle\Entity\Album();
        $albumProfile->setName('Profile');
        $albumProfile->setUser($this);
        $this->addAlbum($albumProfile);

        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
        $postWall = new \Core\BlogBundle\Entity\Post();
        $postWall->setContent('Hey Welcome !');
        $postWall->setUser($this);
        $this->addPost($postWall);

        $this->friendGroups = new \Doctrine\Common\Collections\ArrayCollection();

        $generalGroup = new FriendGroups();
        $generalGroup->setUser($this);
        $generalGroup->setName('general');
        $this->addFriendGroup($generalGroup);

        $waitGroup = new FriendGroups();
        $waitGroup->setUser($this);
        $waitGroup->setName('wait');
        $this->addFriendGroup($waitGroup);

        $this->imageProfile = 'anon_user.png';

        parent::__construct();
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
     * Add characterSheet
     *
     * @param \Core\GalleryBundle\Entity\Album $characterSheet
     * @return User
     */
    public function addCharacterSheet(\Core\CharacterSheetBundle\Entity\CharacterSheet $characterSheet)
    {
        $this->characterSheet[] = $characterSheet;

        return $this;
    }

    /**
     * Remove characterSheet
     *
     * @param \Core\CharacterSheetBundle\Entity\CharacterSheet $characterSheet
     */
    public function removeCharacterSheet(\Core\CharacterSheetBundle\Entity\CharacterSheet $characterSheet)
    {
        $this->characterSheet->removeElement($characterSheet);
    }

    /**
     * Get characterSheet
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCharacterSheets()
    {
        return $this->characterSheet;
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

    /**
     * Set imageProfile
     *
     * @param string $imageProfile
     * @return User
     */
    public function setImageProfile($imageProfile)
    {
        $this->imageProfile = $imageProfile;

        return $this;
    }

    /**
     * Get imageProfile
     *
     * @return string 
     */
    public function getImageProfile()
    {
        return $this->imageProfile;
    }
}
