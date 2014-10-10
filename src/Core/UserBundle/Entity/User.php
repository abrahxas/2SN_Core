<?php

namespace Core\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

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
     * Constructor
     */
    public function __construct()
    {
      $this->albums = new \Doctrine\Common\Collections\ArrayCollection();
      $albumMur = new \Core\GalleryBundle\Entity\Album();
      $albumMur->setName('Mur');
      $albumMur->setUser($this);
      $this->addAlbum($albumMur);

      $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
      $postMur = new \Core\BlogBundle\Entity\Post();
      $postMur->setContent('Hey Welcome !');
      $postMur->setUser($this);
      $this->addPost($postMur);

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
}
