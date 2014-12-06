<?php

namespace Core\MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\UserBundle\Entity\User;
use Core\MessageBundle\Entity\Participant;
use Core\MessageBundle\Form\Type\AddChannelType;
use Core\MessageBundle\Form\Type\AddParticipantType;
use Core\MessageBundle\Form\Type\AddMessageType;
use Core\MessageBundle\Entity\Channel;
use Core\MessageBundle\Entity\Message;
use Symfony\Component\HttpFoundation\Request;

class MessagesController extends Controller
{
    /**
    *@return array
    *$View()
    */
    public function getMessageAction($channelId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $channel = $entityManager->getRepository('CoreMessageBundle:Channel')->find($channelId);
        
        return array('messages'=>$channel->getMessages());
    }

    /**
    *@return array
    *$View()
    */
     public function deleteMessageAction($messageId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $message = $entityManager->getRepository('CoreMessageBundle:Message')->find($messageId);
        $channel = $message->getChannel();

        if($message)
        {
            if($channel->getUsers()[0]->getId() == $user->getId() || $user->getId() == $message->getSender()->getId())
            {
                $entityManager->remove($message);
                $entityManager->flush();
            }
        }

        return array('code' => 200, 'text'=> 'DELETE OK');
    }

    /**
    *@return array
    *$View()
    */
    public function postMessageAction(Request $request, $channelId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new AddMessageType(), $newMessage = new Message());
        $channel = $entityManager->getRepository('CoreMessageBundle:Channel')->find($channelId);
        
        $jsonPost = json_decode($request->getContent(),true);
        if ($request->isMethod('POST')) 
        {
            $form->bind($jsonPost);
            if ($form->isValid())
            {
                $message = $form->get('contents')->getdata();

                $newMessage->setSender($user);
                $newMessage->setChannel($channel);
                $newMessage->setContents($message);
                
                $entityManager->persist($newMessage);
                $entityManager->flush();

                $channel->addMessage($newMessage);

                return array('code' => 200, 'text'=> 'POST OK');
            }
        }
        return array('code' => 400, $form);
    }
}