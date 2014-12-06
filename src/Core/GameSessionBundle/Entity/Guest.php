<?php

namespace Core\GameSessionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\GameSessionBundle\Entity\GameSession;
use Core\UserBundle\Entity\User;

/**
 * Guest
 *
 * @ORM\Table(name="guest")
 * @ORM\Entity(repositoryClass="Core\GameSessionBundle\Entity\GuestRepository")
 */
class Guest
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
     * @ORM\ManyToOne(targetEntity="Core\GameSessionBundle\Entity\GameSession", inversedBy="guests")
     * @ORM\JoinColumn(name="game_session_id", referencedColumnName="id", onDelete="CASCADE"))
     */
    private $gameSession;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     */
    private $guest;


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
     * Set gameSession
     *
     * @param \Core\GameSessionBundle\Entity\GameSession $gameSession
     * @return Guest
     */
    public function setGameSession(\Core\GameSessionBundle\Entity\GameSession $gameSession = null)
    {
        $this->gameSession = $gameSession;

        return $this;
    }

    /**
     * Get gameSession
     *
     * @return \Core\GameSessionBundle\Entity\GameSession 
     */
    public function getGameSession()
    {
        return $this->gameSession;
    }

    /**
     * Set guest
     *
     * @param \Core\UserBundle\Entity\User $guest
     * @return Guest
     */
    public function setGuest(\Core\UserBundle\Entity\User $guest)
    {
        $this->guest = $guest;

        return $this;
    }

    /**
     * Get guest
     *
     * @return \Core\UserBundle\Entity\User 
     */
    public function getGuest()
    {
        return $this->guest;
    }
}
