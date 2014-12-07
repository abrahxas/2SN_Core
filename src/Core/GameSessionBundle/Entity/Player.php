<?php

namespace Core\GameSessionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\UserBundle\Entity\User;
use Core\CharacterSheetBundle\Entity\CharacterSheet;

/**
 * Player
 *
 * @ORM\Table(name="player")
 * @ORM\Entity(repositoryClass="Core\GameSessionBundle\Entity\PlayerRepository")
 */
class Player
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
     * @ORM\ManyToOne(targetEntity="Core\GameSessionBundle\Entity\GameSession", inversedBy="players")
     * @ORM\JoinColumn(name="game_session_id", referencedColumnName="id", onDelete="CASCADE", nullable=false))
     */
    private $gameSession;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Core\CharacterSheetBundle\Entity\CharacterSheet", inversedBy="players")
     * @ORM\JoinColumn(name="charactersheet_id", referencedColumnName="id", onDelete="CASCADE"))
     */
    private $characterSheet;

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
     * @param  \Core\GameSessionBundle\Entity\GameSession $gameSession
     * @return Player
     */
    public function setGameSession(\Core\GameSessionBundle\Entity\GameSession $gameSession)
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
     * Set user
     *
     * @param  \Core\UserBundle\Entity\User $user
     * @return Player
     */
    public function setUser(\Core\UserBundle\Entity\User $user)
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
     * Get characterSheet
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCharacterSheet()
    {
        return $this->characterSheet;
    }

    /**
     * Set characterSheet
     *
     * @param  \Core\CharacterSheetBundle\Entity\CharacterSheet $characterSheet
     * @return Player
     */
    public function setCharacterSheet(\Core\CharacterSheetBundle\Entity\CharacterSheet $characterSheet = null)
    {
        $this->characterSheet = $characterSheet;

        return $this;
    }
}
