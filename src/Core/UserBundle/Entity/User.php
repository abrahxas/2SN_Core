<?php

namespace Core\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="fos_user")
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
     * @ORM\Column(name="birthDate", type="datetime", nullable=true)
     */
    private $birthDate = null;

    /**
     * @ORM\OneToMany(targetEntity="Core\BlogBundle\Entity\Post", mappedBy="user")
     */
    protected $posts;

    /**
     * @ORM\OneToMany(targetEntity="Core\GalleryBundle\Entity\Album", mappedBy="user")
     */
    protected $album;


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
     * Constructor
     */
    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct();
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
     * Add album
     *
     * @param \Core\GalleryBundle\Entity\Album $album
     * @return User
     */
    public function addAlbum(\Core\GalleryBundle\Entity\Album $album)
    {
        $this->album[] = $album;

        return $this;
    }

    /**
     * Remove album
     *
     * @param \Core\GalleryBundle\Entity\Album $album
     */
    public function removeAlbum(\Core\GalleryBundle\Entity\Album $album)
    {
        $this->album->removeElement($album);
    }

    /**
     * Get album
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAlbum()
    {
        return $this->album;
    }
}
