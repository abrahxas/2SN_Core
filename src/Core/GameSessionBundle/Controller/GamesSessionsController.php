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

class GamesSessionsController extends Controller
{
    /**
    *@return array
    *$View()
    */
    public function getGameSessionsAction()
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
        
        return array('listGameSessionMaster' => $listGameSessionMaster,'listGameSessionPlayer' => $listGameSessionPlayer);
    }

    /**
    *@return array
    *$View()
    */
    public function getDetailGameSessionAction($GameSessionId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $gameSession = $entityManager->getRepository('CoreGameSessionBundle:GameSession')->find($GameSessionId);

        return array('GameSession' => $gameSession);
    }

    /**
    *@return array
    *$View()
    */
    public function postGameSessionAction(Request $request)
    {
    	$entityManager = $this->getDoctrine()->getManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$form = $this->createForm(new CreateGameSessionType(), $gameSession = new GameSession());
        
        $jsonPost = json_decode($request->getContent(),true);

    	if($request->isMethod('POST')){
    		$form->bind($jsonPost);
    		if($form->isValid()){
                $gameSession->setMaster($user);
                $gameSession->setName($form->get('name')->getData());
                $gameSession->setDescription($form->get('description')->getData());
    			$entityManager->persist($gameSession);
    			$entityManager->flush();

    			return array('code' => 200, 'text'=> 'POST OK');
    		}
    	}
    	return array('code' => 400, $form);
    }

    /**
    *@return array
    *$View()
    */
    public function postGuestAction(Request $request, $GameSessionId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new AddGuestType());

        $jsonPost = json_decode($request->getContent(),true);

        if($request->isMethod('POST')){
            $form->bind($jsonPost);
            if($form->isValid()){
                $gameSession = $entityManager->getRepository('CoreGameSessionBundle:GameSession')->find($GameSessionId);
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

                return array('code' => 200, 'text'=> 'POST OK');
            }
        }
        return array('code' => 400, $form);
    }

    /**
    *@return array
    *$View()
    */
    public function getInvitationAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $listInvitation = $entityManager->getRepository('CoreGameSessionBundle:Guest')->findBy(array('guest'=>$user->getId()));

        return array('listInvitation' => $listInvitation);
    }

    /**
    *@return array
    *$View()
    */
    public function postValidationAction(Request $request, $GameSessionId, $invitationId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new SelectCharacterSheetType($user), $newPlayer = new player());

        $jsonPost = json_decode($request->getContent(),true);


        if ($request->isMethod('Post')) 
        {
            $form->bind($jsonPost);
            if($form->isValid())
            {
                $gameSession = $entityManager->getRepository('CoreGameSessionBundle:GameSession')->find($GameSessionId);
                $invitation = $entityManager->getRepository('CoreGameSessionBundle:Guest')->find($invitationId);

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

                return array('code' => 200, 'text'=> 'POST OK');
            }
                
        }
        return array('code' => 400, $form);
    }

    /**
    *@return array
    *$View()
    */
    public function deleteGameSessionAction(Request $request, $GameSessionId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        if($request->isMethod('Get'))
        {
            $gameSession = $entityManager->getRepository('CoreGameSessionBundle:GameSession')->find($GameSessionId);
            if($gameSession->getMaster() == $user)
            {
                $entityManager->remove($gameSession);
                $entityManager->flush();
                return array('code' => 200, 'text'=> 'DELETE OK');
            }    
        }
        return array('code' => 400, 'text'=> 'DELETE KO');
    }

    /**
    *@return array
    *$View()
    */
    public function deletePlayerAction(Request $request)
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
                        return array('code' => 200, 'text'=> 'DELETE OK');
                    }
                }
            }    
        }
        return array('code' => 400, 'text'=> 'DELETE KO');
    }

    /**
    *@return array
    *$View()
    */
    public function deleteInvitationAction($GameSessionId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $gameSession = $entityManager->getRepository('CoreGameSessionBundle:GameSession')->find($GameSessionId);
        $listGuest = $gameSession->getGuests();
        foreach ($listGuest as $guest) 
        {
            if($guest->getGuest() == $user)
            {
                $entityManager->remove($guest);
                $entityManager->flush();
                return array('code' => 200, 'text'=> 'DELETE OK');
            }
        }
        return array('code' => 400, 'text'=> 'DELETE KO');
    }

    /**
    *@return array
    *$View()
    */
    public function postUpdateGameSessionAction(Request $request, $GameSessionId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $gameSession = $entityManager->getRepository('CoreGameSessionBundle:GameSession')->find($GameSessionId);
        $form = $this->createForm(new CreateGameSessionType(),$gameSession);

        $jsonPost = json_decode($request->getContent(),true);

        if($request->isMethod('POST')){
            $form->bind($jsonPost);
            if($form->isValid()){
                $gameSession->setMaster($user);
                $gameSession->setName($form->get('name')->getData());
                $gameSession->setDescription($form->get('description')->getData());
                $entityManager->persist($gameSession);
                $entityManager->flush();

                return array('code' => 200, 'text'=> 'POST OK');
            }
        }
        return array('code' => 400, $form);
    }
}


