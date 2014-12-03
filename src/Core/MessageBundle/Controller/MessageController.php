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

class MessageController extends Controller
{
    public function indexMessageAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $channel = $entityManager->getRepository('CoreMessageBundle:Channel')->find($request->get('channelId'));
        
        return $this->render('CoreMessageBundle:default:indexmessage.html.twig', array(
                    'messages'=>$channel->getMessages()
        ));
    }

     public function deleteMessageAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $message = $entityManager->getRepository('CoreMessageBundle:Message')->find($request->get('messageId'));
        $channel = $message->getChannel();

        if($message)
        {
            if($channel->getUsers()[0]->getId() == $user->getId() || $user->getId() == $message->getSender()->getId())
            {
                $entityManager->remove($message);
                $entityManager->flush();
            }
        }

        return $this->redirect($this->generateUrl('core_message_homepage'));
    }

    public function addMessageAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new AddMessageType(), $newMessage = new Message());
        $channel = $entityManager->getRepository('CoreMessageBundle:Channel')->find($request->get('channelId'));
        
        if ($request->isMethod('POST')) 
        {
            $form->handleRequest($request);
            if ($form->isValid())
            {
                $message = $form->get('contents')->getdata();

                $newMessage->setSender($user);
                $newMessage->setChannel($channel);
                $newMessage->setContents($message);
                
                $entityManager->persist($newMessage);
                $entityManager->flush();

                $channel->addMessage($newMessage);

                return $this->redirect($this->generateUrl('core_message_homepage'));
            }
        }
        return $this->render('CoreMessageBundle:default:addmessage.html.twig', array(
                    'form'=> $form->createView()
        ));
    }
}