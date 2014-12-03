<?php

namespace Core\GameSessionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Core\GameSessionBundle\Form\Type\CreateGameSessionType;
use Core\GameSessionBundle\Form\Type\AddGuestType;
use Core\GameSessionBundle\Entity\GameSession;
use Core\MessageBundle\Entity\Channel;
use Core\GameSessionBundle\Entity\Guest;
use Core\UserBundle\Entity\User;
use Core\GameSessionBundle\Entity\Player;

class playerController extends Controller
{
    
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $listGameSessionMaster = $entityManager->getRepository('CoreGameSessionBundle:Player')->findBy(array('master' => $user));
        $players = $entityManager->getRepository('CoreGameSessionBundle:Player')->findBy(array('user' => $user));
        $listGameSessionPlayer = new \Doctrine\Common\Collections\ArrayCollection();

        foreach ($players as $p) {
            if($p->getUser = $user)
            {   
                $listGameSessionPlayer[] = $p->getGameSession();
            }
        }
        
        return $this->render('CoreGameSessionBundle:default:index.html.twig', array(
            'listGameSessionMaster' => $listGameSessionMaster,
            'listGameSessionPlayer' => $listGameSessionPlayer
        ));
    }
}


