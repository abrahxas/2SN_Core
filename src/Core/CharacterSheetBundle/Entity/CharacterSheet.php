<?php

namespace Core\CharacterSheetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Core\PlayerBundle\Entity\Player;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Album
 *
 * @ORM\Table(name="character_sheet")
 * @ORM\Entity
 * @Vich\Uploadable
 */
class CharacterSheet
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $fullName;

    /**
     * @var string
     *
     * @ORM\Column(name="details", type="string", length=255)
     */
    private $details;

    /**
     * @var string
     *
     * @ORM\Column(name="background", type="text")
     */
    private $background;

    /**
     * @Vich\UploadableField(mapping="photo", fileNameProperty="imageName")
     *
     * @var File $imageFile
     */
    protected $imageFile;

    /**
     * @var string
     *
     * @ORM\Column(name="image_name", type="string", length=255)
     */
    private $imageName;

    /**
     * @Vich\UploadableField(mapping="characterSheet", fileNameProperty="sheetName")
     *
     * @var File $sheetFile
     */
    protected $sheetFile;

    /**
     * @var string
     *
     * @ORM\Column(name="sheet_name", type="string", length=255)
     */
    private $sheetName;

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
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="characterSheets")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE"))
     */
    private $user;

    /**
     *@var \Core\GameSessionBundle\Entity\Player[]
     * @ORM\OneToMany(targetEntity="Core\GameSessionBundle\Entity\Player", mappedBy="characterSheet",cascade={"persist"})
     */
    private $players;

    public function __construct()
    {
        $this->createdAt = new \Datetime();
        $this->updatedAt = new \Datetime();
        $this->players = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set fullName
     *
     * @param  string $fullName
     * @return Album
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime $createdAt
     * @return Album
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
     * @param  \DateTime $updatedAt
     * @return Album
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
     * @param  \Core\UserBundle\Entity\User $user
     * @return Album
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
     * Set imageName
     *
     * @param  string         $imageName
     * @return CharacterSheet
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get imageName
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageFile(File $image)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Set sheetName
     *
     * @param  string         $sheetName
     * @return CharacterSheet
     */
    public function setSheetName($sheetName)
    {
        $this->sheetName = $sheetName;

        return $this;
    }

    /**
     * Get sheetName
     *
     * @return string
     */
    public function getSheetName()
    {
        return $this->sheetName;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $sheet
     */
    public function setSheetFile(File $sheet)
    {
        $this->sheetFile = $sheet;

        if ($sheet) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @return File
     */
    public function getSheetFile()
    {
        return $this->sheetFile;
    }

    /**
     * Set details
     *
     * @param  string         $details
     * @return CharacterSheet
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set background
     *
     * @param  string         $background
     * @return CharacterSheet
     */
    public function setBackground($background)
    {
        $this->background = $background;

        return $this;
    }

    /**
     * Get background
     *
     * @return string
     */
    public function getBackground()
    {
        return $this->background;
    }

    /**
     * Get players
     *
     * @return \Core\PlayerBundle\Entity\Player
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Add players
     *
     * @param  \Core\GameSessionBundle\Entity\Player $players
     * @return CharacterSheet
     */
    public function addPlayer(\Core\GameSessionBundle\Entity\Player $players)
    {
        $this->players[] = $players;

        return $this;
    }

    /**
     * Remove players
     *
     * @param \Core\GameSessionBundle\Entity\Player $players
     */
    public function removePlayer(\Core\GameSessionBundle\Entity\Player $players)
    {
        $this->players->removeElement($players);
    }
}
