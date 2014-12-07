<?php

namespace Core\MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\UserBundle\Entity\User;
use Core\MessageBundle\Entity\Participant;
use Core\MessageBundle\Entity\Channel;
use Core\MessageBundle\Entity\Message;
use Symfony\Component\HttpFoundation\Request;

class ChannelController extends Controller
{
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $channels = $user->getChannels();
        $conn = $this->container->get('database_connection');
        $query = "SELECT fd.name, fd.friend_id as id
                FROM friend fd
                inner join friend_groups fg on fg.id = fd.friendgroup_id
                inner join user u on u.id = fg.user_id
                where u.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $user->getId());
        $stmt->execute();

        return $this->render('CoreMessageBundle:default:index.html.twig', array(
            'channels' => $channels,
            'friends' => $stmt,
        ));
    }

    public function addChannelAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $newParticipant = $entityManager->getRepository('CoreUserBundle:User')->find($request->get('friendId'));

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

        return $this->redirect($this->generateUrl('core_message_homepage'));
    }

    public function addParticipantAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        $channel = $entityManager->getRepository('CoreMessageBundle:Channel')->find($request->get('channelId'));
        $newUser = $entityManager->getRepository('CoreUserBundle:User')->find($request->get('friendId'));

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

        return $this->redirect($this->generateUrl('core_message_homepage'));
    }

    public function deleteChannelAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $channel = $entityManager->getRepository('CoreMessageBundle:Channel')->find($request->get('channelId'));

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

        return $this->redirect($this->generateUrl('core_message_homepage'));
    }

    public function removeParticipantAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $channel = $entityManager->getRepository('CoreMessageBundle:Channel')->find($request->get('channelId'));
        $deleteparticipant = $entityManager->getRepository('CoreUserBundle:User')->find($request->get('participantId'));

        if ($channel->getUsers()[0]->getId() == $user->getId()) {
            $channel->removeUser($deleteparticipant);
            $deleteparticipant->removeChannel($channel);
        } elseif ($deleteparticipant->getId() == $user->getId()) {
            $user->removeChannel($channel);
            $channel->removeParticipant($deleteparticipant);
        }
        $this->changeChannelName($channel);

        $entityManager->persist($channel);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('core_message_homepage'));
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
