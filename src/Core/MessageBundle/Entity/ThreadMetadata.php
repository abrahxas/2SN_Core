<?php

namespace Core\MessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Entity\ThreadMetadata as BaseThreadMetadata;

/**
 * @ORM\Entity
 */
class ThreadMetadata extends BaseThreadMetadata
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\ManyToOne(
   *   targetEntity="Core\MessageBundle\Entity\Thread",
   *   inversedBy="metadata"
   * )
   * @var \FOS\MessageBundle\Model\ThreadInterface
   */
  protected $thread;

  /**
   * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
   * @var \FOS\MessageBundle\Model\ParticipantInterface
   */
  protected $participant;
}