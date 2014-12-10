<?php

namespace Core\GameSessionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Core\MessageBundle\Entity\Channel;

/**
 * GameSession
 *
 * @ORM\Table(name="game_session")
 * @ORM\Entity(repositoryClass="Core\GameSessionBundle\Entity\GameSessionRepository")
 */
class GameSession
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
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User", inversedBy="gameSessions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id"), onDelete="CASCADE")
     */
    private $master;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var \Core\MessageBundle\Entity\Channel[]
     * @ORM\OneToMany(targetEntity="Core\MessageBundle\Entity\Channel", mappedBy="gameSession", cascade={"persist"})
     */
    protected $channels;

    /**
     *@var Core\GameSessionBundle\Entity\Player[]
     * @ORM\OneToMany(targetEntity="Core\GameSessionBundle\Entity\Player", mappedBy="gameSession", cascade={"persist"})
     */
    private $players;

    /**
     *@var Core\GameSessionBundle\Entity\Guest[]
     * @ORM\OneToMany(targetEntity="Core\GameSessionBundle\Entity\Guest", mappedBy="gameSession", cascade={"persist"})
     */
    private $guests;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->channels = new \Doctrine\Common\Collections\ArrayCollection();
        $this->players = new \Doctrine\Common\Collections\ArrayCollection();
        $this->guests = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param  string      $name
     * @return GameSession
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param  string      $description
     * @return GameSession
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set master
     *
     * @param  \Core\UserBundle\Entity\User $master
     * @return GameSession
     */
    public function setMaster(\Core\UserBundle\Entity\User $master = null)
    {
        $this->master = $master;

        return $this;
    }

    /**
     * Get master
     *
     * @return \Core\UserBundle\Entity\User
     */
    public function getMaster()
    {
        return $this->master;
    }

    /**
     * Add channels
     *
     * @param  \Core\MessageBundle\Entity\Channel $channels
     * @return GameSession
     */
    public function addChannel(\Core\MessageBundle\Entity\Channel $channels)
    {
        $this->channels[] = $channels;

        return $this;
    }

    /**
     * Remove channels
     *
     * @param \Core\MessageBundle\Entity\Channel $channels
     */
    public function removeChannel(\Core\MessageBundle\Entity\Channel $channels)
    {
        $this->channels->removeElement($channels);
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
     * Add players
     *
     * @param  \Core\GameSessionBundle\Entity\Player $players
     * @return GameSession
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

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Add guests
     *
     * @param  \Core\GameSessionBundle\Entity\Guest $guests
     * @return GameSession
     */
    public function addGuest(\Core\GameSessionBundle\Entity\Guest $guests)
    {
        $this->guests[] = $guests;

        return $this;
    }

    /**
     * Remove guests
     *
     * @param \Core\GameSessionBundle\Entity\Guest $guests
     */
    public function removeGuest(\Core\GameSessionBundle\Entity\Guest $guests)
    {
        $this->guests->removeElement($guests);
    }

    /**
     * Get guests
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGuests()
    {
        return $this->guests;
    }
}
