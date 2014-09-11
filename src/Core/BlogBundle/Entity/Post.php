<?php

namespace Core\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
//use Symfony\Component\HttpFoundation\File\File;
//use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Post
 *
 * @ORM\Table()
 * @ORM\Entity()
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
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id"))
     */
    private $user;

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
     * Set content
     *
     * @param string $content
     * @return Post
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
     * @return Post
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

    /**
     * Set user
     *
     * @param \src\Core\UserBundle\Entity\User $user
     * @return Post
     */
    public function setUser(\Core\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \src\Core\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
