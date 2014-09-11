<?php

namespace Core\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\HttpFoundation\File\File;
//use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Post
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\BlogBundle\Entity\PostRepository")
 */
class Post
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
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

//    /**
//     * @Vich\UploadableField(mapping="blog", fileNameProperty="imageName")
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


    public function __construct($user)
    {
        $this->createdAt = new \Datetime();
        $this->updatedAt = new \Datetime();
        $this->author = $user;
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
     * Set content
     *
     * @param string $content
     * @return Article
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Article
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
     * Set author
     *
     * @param string $author
     * @return Article
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

//    /**
//     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
//     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
//     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
//     * must be able to accept an instance of 'File' as the bundle will inject one here
//     * during Doctrine hydration.
//     *
//     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
//     */
//    public function setImageFile(File $image)
//    {
//        $this->imageFile = $image;
//
//        if ($image) {
//            $this->updatedAt = new \DateTime('now');
//        }
//    }
//
//    /**
//     * @return File
//     */
//    public function getImageFile()
//    {
//        return $this->imageFile;
//    }
//
//    /**
//     * Set imageName
//     *
//     * @param string $imageName
//     * @return Article
//     */
//    public function setImageName($imageName)
//    {
//        $this->imageName = $imageName;
//
//        return $this;
//    }
//
//    /**
//     * Get imageName
//     *
//     * @return string
//     */
//    public function getImageName()
//    {
//        return $this->imageName;
//    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Post
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
}
