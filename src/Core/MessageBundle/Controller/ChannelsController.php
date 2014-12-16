<?php

namespace Core\MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use Core\UserBundle\Entity\User;
use Core\MessageBundle\Entity\Participant;
use Core\MessageBundle\Entity\Channel;
use Core\MessageBundle\Entity\Message;

class ChannelsController extends Controller
{
    /**
    *@return array
    *$View()
    */
    public function getChannelsAction($userId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $channels = $user->getChannels();

        return array(
            'channels' => $channels,
        );
    }

    /**
    *@return array
    *$View()
    * @Get("/channel/{channelId}")
    */
    public function getChannelAction($channelId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $channel = $entityManager->getRepository('CoreMessageBundle:Channel')->find($channelId);

        return array(
            'channel' => $channel,
        );
    }

    /**
    *@return array
    *$View()
    */
     public function postChannelAction($userId)
     {
         $entityManager = $this->getDoctrine()->getManager();
         $user = $this->container->get('security.context')->getToken()->getUser();
         $newParticipant = $entityManager->getRepository('CoreUserBundle:User')->find($userId);

         $channel = new Channel();
         $channel->addUser($user);
         $channel->addUser($newParticipant);
         $channel->setName("Private_Channel_".$user->getUserName()."_".$newParticipant->getUserName());

         $entityManager->persist($channel);

         $user->addChannel($channel);
         $newParticipant->addChannel($channel);

         $entityManager->persist($user);
         $entityManager->persist($newParticipant);

         $entityManager->flush();

         return array(
            'code' => 200,
            'data' => $channel,
        );
     }

    /**
    *@return array
    *$View()
    */
    public function postParticipantFriendAction(Request $request, $channelId, $userId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $jsonPost = json_decode($request->getContent(), true);
        $channel = $entityManager->getRepository('CoreMessageBundle:Channel')->find($channelId);
        $newUser = $entityManager->getRepository('CoreUserBundle:User')->find($userId);

        $listParticipant = $channel->getUsers();

        foreach ($listParticipant as $participant) {
            if ($newUser->getId() == $participant->getId()) {
                return $this->redirect($this->generateUrl('core_message_homepage'));
            }
        }

        $channel->addUser($newUser);
        $channel->setName($channel->getName()."_".$newUser->getUserName());
        $newUser->addChannel($channel);
        $entityManager->persist($channel);
        $entityManager->flush();

        return array(
            'code' => 200,
            'data' => $channel,
        );
    }

    /**
    *@return array
    *$View()
    */
    public function deleteChannelAction(Request $request, $channelId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $channel = $entityManager->getRepository('CoreMessageBundle:Channel')->find($channelId);

        if (!$channel) {
            throw $this->createNotFoundException('Channel Not Found');
        }

        $listParticipant = $channel->getUsers();
        $ListMessage = $channel->getMessages();
        $creator = $listParticipant[0];

        if ($creator->getId() == $user->getId()) {
            foreach ($ListMessage as $message) {
                $channel->removeMessage($message);
            }
            foreach ($listParticipant as $participant) {
                $channel->removeUser($participant);
                $participant->removeChannel($channel);
            }
            $entityManager->remove($channel);
        } else {
            foreach ($listParticipant as $participant) {
                if ($user->getId() == $participant->getId()) {
                    $participant->removeChannel($channel);
                    $channel->removeUser($participant);
                    $this->changeChannelName($channel);
                    if ($listParticipant->count() == 1) {
                        $newMessage = new Message();
                        $newMessage->setChannel($channel);
                        $newMessage->setContents("You are alone in the channel");
                        $entityManager->persist($newMessage);
                        $channel->addMessage($newMessage);
                    }
                }
            }
        }
        $entityManager->flush();

        return array(
            'code' => 200,
            'data' => 'Delete done',
        );
    }

    /**
    *@return array
    *$View()
    */
    public function deleteFromchannelParticipantAction(Request $request, $channelId, $participantId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $channel = $entityManager->getRepository('CoreMessageBundle:Channel')->find($channelId);
        $deleteparticipant = $entityManager->getRepository('CoreUserBundle:User')->find($participantId);

        if ($channel->getUsers()[0]->getId() == $user->getId()) {
            $channel->removeUser($deleteparticipant);
            $deleteparticipant->removeChannel($channel);
        } elseif ($deleteparticipant->getId() == $user->getId()) {
            $channel->removeUser($deleteparticipant);
            $user->removeChannel($channel);
        }
        $this->changeChannelName($channel);

        $entityManager->persist($channel);
        $entityManager->flush();

        return array(
            'code' => 200,
            'data' => $channel,
        );
    }

    private function changeChannelName(Channel $channel)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $listParticipant = $channel->getUsers();

        $channel->setName("Private_Channel");
        foreach ($listParticipant as $participant) {
            $channel->setName($channel->getName()."_".$participant->getUserName());
        }
        $entityManager->persist($channel);
        $entityManager->flush();
    }
}
