<?php
namespace Core\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use FOS\UserBundle\Model\User as BaseUser;
use Core\FriendListBundle\Entity\FriendGroups;

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
     * @Exclude
     */
    private $birthDate = null;

    /**
     * @var Core\FriendListBundle\Entity\friendGroup[]
     * @ORM\OneToMany(targetEntity="Core\FriendListBundle\Entity\FriendGroups", mappedBy="user", cascade={"persist"})
     * @var string
     *
     * @ORM\Column(name="image_profile", type="string", nullable=true)
     */
    private $imageProfile;

    /**
     * @var friendGroups[]
     * @ORM\OneToMany(targetEntity="Core\FriendListBundle\Entity\FriendGroups", mappedBy="user", cascade={"all"})
     * @Exclude
     */
    protected $friendGroups;

    /**
     * @var Core\MessageBundle\Entity\channel[]
     * @ORM\ManyToMany(targetEntity="Core\MessageBundle\Entity\Channel", mappedBy="users", cascade={"persist"})
     * @Exclude
     */
    protected $channels;

    /**
     * @var \Core\BlogBundle\Entity\Post[]
     * @ORM\OneToMany(targetEntity="Core\BlogBundle\Entity\Post", mappedBy="user", cascade={"persist"})
     * @Exclude
     */
    protected $posts;

    /**
     * @var \Core\CommentBundle\Entity\Comment[]
     * @ORM\OneToMany(targetEntity="Core\CommentBundle\Entity\Comment", mappedBy="user", cascade={"persist"})
     * @Exclude
     */
    protected $comments;

    /**
     * @var \Core\GalleryBundle\Entity\Album[]
     * @ORM\OneToMany(targetEntity="Core\GalleryBundle\Entity\Album", mappedBy="user", cascade={"persist"})
     * @Exclude
     */
    protected $albums;

    /**
     * @var \Core\GameSessionBundle\Entity\GameSession[]
     * @ORM\OneToMany(targetEntity="Core\GameSessionBundle\Entity\GameSession", mappedBy="master", cascade={"persist"})
     * @Exclude
     */
    protected $gameSessions;

    /**
     * @var \Core\CharacterSheetBundle\Entity\CharacterSheet[]
     * @ORM\OneToMany(targetEntity="Core\CharacterSheetBundle\Entity\CharacterSheet", mappedBy="user", cascade={"persist"})
     * @Exclude
     */
    protected $characterSheets;

    /**
     * @var \Core\MessageBundle\Entity\Message[]
     * @ORM\OneToMany(targetEntity="Core\MessageBundle\Entity\Message", mappedBy="sender", cascade={"persist"})
     * @Exclude
     */
    protected $sender;

    /**
     * @var \Core\FiendListBundle\Entity\Friend[]
     * @ORM\OneToMany(targetEntity="Core\FriendListBundle\Entity\Friend", mappedBy="friend", cascade={"persist"})
     * @Exclude
     */
    protected $friends;

    /**
     * @var \Core\FiendListBundle\Entity\Friend[]
     * @ORM\OneToMany(targetEntity="Core\FriendListBundle\Entity\Friend", mappedBy="user", cascade={"persist"})
     * @Exclude
     */
    protected $userFriend;

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
        $this->channels = new \Doctrine\Common\Collections\ArrayCollection();
        $this->gameSessions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->characterSheets = new \Doctrine\Common\Collections\ArrayCollection();

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
     * @param  \DateTime $birthDate
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
     * Add friendGroups
     *
     * @param  \Core\FriendListBundle\Entity\FriendGroups $friendGroups
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
     * Add channel
     *
     * @param  \Core\MessageBundle\Entity\Channel $channel
     * @return User
     */
    public function addChannel(\Core\MessageBundle\Entity\Channel $channel)
    {
        $this->channels[] = $channel;

        return $this;
    }

    /**
     * Remove channel
     *
     * @param \Core\MessageBundle\Entity\Channel $channel
     */
    public function removeChannel(\Core\MessageBundle\Entity\Channel $channel)
    {
        $this->channels->removeElement($channel);
    }

    /**
     * Get channels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * Add posts
     *
     * @param  \Core\BlogBundle\Entity\Post $posts
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
     * @param  \Core\GalleryBundle\Entity\Album $albums
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
     * @param  \Core\GalleryBundle\Entity\Album $characterSheet
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
     * @param  \Core\GameSessionBundle\Entity\GameSession $gameSessions
     * @return User
     */
    public function addGameSession(\Core\GameSessionBundle\Entity\GameSession $gameSessions)
    {
        $this->gameSessions[] = $gameSessions;

        return $this;
    }

    /**
     * Remove gameSessions
     *
     * @param \Core\GameSessionBundle\Entity\GameSession $gameSessions
     */
    public function removeGameSession(\Core\GameSessionBundle\Entity\GameSession $gameSessions)
    {
        $this->gameSessions->removeElement($gameSessions);
    }

    /**
     * Get gameSessions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGameSessions()
    {
        return $this->gameSessions;
    }

    /**
     * Set imageProfile
     *
     * @param  string $imageProfile
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
