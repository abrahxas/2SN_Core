<?php

namespace Core\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\HttpFoundation\File\File;
//use Vich\UploaderBundle\Mapping\Annotation as Vich;
//use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Picture
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\GalleryBundle\Entity\Gallery\PhotoRepository")
 */
class Photo
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

//    /**
//     * @var string
//     * @Gedmo\Slug(fields={"title"}, separator="-", unique=true)
//     * @ORM\Column(name="slug", type="string", length=255)
//     */
//    private $slug;

//    /**
//     * @Vich\UploadableField(mapping="gallery", fileNameProperty="imageName")
//     *
//     * @var File $imageFile
//     */
//    protected $imageFile;
//
//    /**
//     * @var string
//     *
//     * @ORM\Column(name="imageName", type="string", length=255)
//     */
//    private $imageName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Album", inversedBy="picture")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id"))
     */
    private $album;

    /**
     * @ORM\ManyToOne(targetEntity="\Core\UserBundle\Entity\User", inversedBy="photo")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id"))
     */
    private $user;

    public function __construct()
    {
        $this->createdAt = new \Datetime();
        $this->updatedAt = new \Datetime();
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
     * Set title
     *
     * @param string $title
     * @return Picture
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Photo
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Photo
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set user
     *
     * @param \Core\UserBundle\Entity\User $user
     * @return Photo
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
     * Set album
     *
     * @param \Core\GalleryBundle\Entity\Album $album
     * @return Photo
     */
    public function setAlbum(\Core\GalleryBundle\Entity\Album $album = null)
    {
        $this->album = $album;

        return $this;
    }

    /**
     * Get album
     *
     * @return \Core\GalleryBundle\Entity\Album 
     */
    public function getAlbum()
    {
        return $this->album;
    }
}
