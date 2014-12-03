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
use Core\GameSessionBundle\Form\Type\SelectCharacterSheetType;

class GameSessionController extends Controller
{
    
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $listGameSessionMaster = $entityManager->getRepository('CoreGameSessionBundle:GameSession')->findBy(array('master' => $user));
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

    public function detailGameSessionAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $gameSession = $entityManager->getRepository('CoreGameSessionBundle:GameSession')->find($request->get('GameSessionId'));

        return $this->render('CoreGameSessionBundle:default:detailSession.html.twig', array(
            'GameSession' => $gameSession
        ));
    }

    public function createAction(Request $request)
    {
    	$entityManager = $this->getDoctrine()->getManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$form = $this->createForm(new CreateGameSessionType(), $gameSession = new GameSession());

    	if($request->isMethod('POST')){
    		$form->handleRequest($request);
    		if($form->isValid()){
                $gameSession->setMaster($user);
                $gameSession->setName($form->get('name')->getData());
                $gameSession->setDescription($form->get('description')->getData());
    			$entityManager->persist($gameSession);
    			$entityManager->flush();

    			return $this->redirect($this->generateUrl('core_gamesession_homepage'));
    		}
    	}
    	return $this->render('CoreGameSessionBundle:default:createGameSession.html.twig', array(
    		'form'=> $form->createView()
		));
    }

    public function addGuestAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new AddGuestType());

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $gameSession = $entityManager->getRepository('CoreGameSessionBundle:GameSession')->find($request->get('GameSessionId'));
                $participant = $entityManager->getRepository('CoreUserBundle:User')->findOneBy(array('username'=>$form->get('username')->getdata()));

                if($participant == $user)
                {
                    throw $this->createNotFoundException('You can not be Master and player at the the time');
                }
                
                $listGameSessionPlayer = $gameSession->getPlayers();

                foreach ($listGameSessionPlayer as $player) {
                    if($player->getUser() == $participant)
                    {
                        throw $this->createNotFoundException('You can not invite a player already present in the part');
                    }
                }

                $listGameSessionGuest = $gameSession->getGuests();
                foreach ($listGameSessionGuest as $guest) {
                    if($guest->getGuest() == $participant)
                    {
                        throw $this->createNotFoundException('An invitation is already in progress for this person');
                    }
                }

                $newGuest = new Guest();
                $newGuest->setGameSession($gameSession);
                $newGuest->setGuest($participant);

                $entityManager->persist($newGuest);
                $entityManager->flush();

                $gameSession->addGuest($newGuest);

                $channels = $gameSession->getChannels();
                if (count($channels) == 0)
                {
                    $channel = new Channel();
                    $channel->setGameSession($gameSession);
                    $channel->addUser($user);
                    $channel->setName($gameSession->getName());
                }
                else
                {
                    $channels = $gameSession->getChannels();
                    $channel = $channels[0];
                }
                
                $entityManager->persist($channel);

                $user->addChannel($channel);

                $entityManager->flush();

                return $this->redirect($this->generateUrl('core_gamesession_homepage'));
            }
        }
        return $this->render('CoreGameSessionBundle:default:addGuest.html.twig', array(
            'form'=> $form->createView()
        ));
    }

    public function invitationAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $listInvitation = $entityManager->getRepository('CoreGameSessionBundle:Guest')->findBy(array('guest'=>$user->getId()));

        return $this->render('CoreGameSessionBundle:default:invitation.html.twig', array(
            'listInvitation' => $listInvitation
        ));
    }

    public function validationAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new SelectCharacterSheetType($user), $newPlayer = new player());


        if ($request->isMethod('Post')) 
        {
            $form->handleRequest($request);
            if($form->isValid())
            {
                $gameSession = $entityManager->getRepository('CoreGameSessionBundle:GameSession')->find($request->get('GameSessionId'));
                $invitation = $entityManager->getRepository('CoreGameSessionBundle:Guest')->find($request->get('invitationId'));

                $guest = $invitation->getGuest();

                $newPlayer->setGameSession($gameSession);
                $newPlayer->setUser($guest);
                $CharacterSheet = $entityManager->getRepository('CoreCharacterSheetBundle:CharacterSheet')->find($form->get('CharacterSheet')->getData());
                $newPlayer->setCharacterSheet($CharacterSheet);

                $entityManager->persist($newPlayer);
                $entityManager->flush();

                $channels = $gameSession->getChannels();
                $channel = $channels[0];

                $channel->addUser($guest);
                $guest->addChannel($channel);
                
                $entityManager->flush();
                $gameSession->addPlayer($newPlayer);

                $entityManager->persist($gameSession);
                $entityManager->remove($invitation);
                $entityManager->flush();

                return $this->redirect($this->generateUrl('core_gamesession_invitation'));
            }
                
        }
      
        return $this->render('CoreGameSessionBundle:default:selectCharacterSheet.html.twig', array(
            'form'=> $form->createView()
        ));
    }

    public function deleteAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        if($request->isMethod('Get'))
        {
            $gameSession = $entityManager->getRepository('CoreGameSessionBundle:GameSession')->find($request->get('GameSessionId'));
            if($gameSession->getMaster() == $user)
            {
                $entityManager->remove($gameSession);
                $entityManager->flush();
            }    
        }
        return $this->redirect($this->generateUrl('core_gamesession_homepage'));
    }

    public function leaveGameSessionAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        if($request->isMethod('Get'))
        {
            $gameSession = $entityManager->getRepository('CoreGameSessionBundle:GameSession')->find($request->get('GameSessionId'));
            if($gameSession->getMaster() != $user)
            {
                $listGameSessionPlayer = $gameSession->getPlayers();
                $listGameSessionChannel = $gameSession->getChannels();

                foreach ($listGameSessionPlayer as $player) 
                {
                    if($player->getUser() == $user)
                    {
                        foreach ($listGameSessionChannel as $channel) 
                        {
                            $listParticipant = $channel->getUsers();

                            foreach ($listParticipant as $participant) 
                            {
                                if($participant == $user)
                                {
                                    $channel->removeUser($user);
                                    $user->removeChannel($channel);
                                }
                            }
                        }
                        $entityManager->remove($player);
                        $entityManager->flush();
                    }
                }
            }    
        }
        return $this->redirect($this->generateUrl('core_gamesession_homepage'));
    }

    public function deleteInvitationAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $gameSession = $entityManager->getRepository('CoreGameSessionBundle:GameSession')->find($request->get('GameSessionId'));
        $listGuest = $gameSession->getGuests();
        foreach ($listGuest as $guest) 
        {
            if($guest->getGuest() == $user)
            {
                $entityManager->remove($guest);
                $entityManager->flush();
            }
        }
        return $this->redirect($this->generateUrl('core_gamesession_invitation'));
    }

}


