<?php

namespace Core\MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Core\UserBundle\Entity\User;
use Core\MessageBundle\Form\Type\AddMessageType;
use Core\MessageBundle\Entity\Channel;
use Core\MessageBundle\Entity\Message;
use FOS\RestBundle\Controller\Annotations\Get;

class MessagesController extends Controller
{
    /**
    *@return array
    *$View()
    */
    public function getMessagesAction($channelId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $channel = $entityManager->getRepository('CoreMessageBundle:Channel')->find($channelId);

        return array(
            'messages' => $channel->getMessages(),
        );
    }

    /**
    *@return array
    *$View()
    * @Get("/message/{messageId}")
    */
    public function getMessageAction($messageId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $message = $entityManager->getRepository('CoreMessageBundle:Message')->find($messageId);


        return array(
            'message' => $message,
        );
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

         if ($message) {
             if ($channel->getUsers()[0]->getId() == $user->getId() || $user->getId() == $message->getSender()->getId()) {
                 $entityManager->remove($message);
                 $entityManager->flush();
             }
         }

         return array(
            'code' => 200,
            'data' => 'Delete done',
        );
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

        $jsonPost = json_decode($request->getContent(), true);
        if ($request->isMethod('POST') && !empty($jsonPost)) {
            $form->bind($jsonPost);
            if ($form->isSubmitted() && $form->isValid()) {
                $message = $form->get('contents')->getdata();

                $newMessage->setSender($user);
                $newMessage->setChannel($channel);
                $newMessage->setContents($message);

                $entityManager->persist($newMessage);
                $entityManager->flush();

                $channel->addMessage($newMessage);

                return array(
                    'code' => 200,
                    'data' => $channel,
                );
            }
        }

        return array(
            'code' => 400,
            $form,
        );
    }
}
