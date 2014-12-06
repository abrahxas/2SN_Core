<?php

namespace Core\FriendListBundle\Controller;

use Core\FriendListBundle\Entity\FriendGroups;
use Core\FriendListBundle\Entity\Friend;
use Core\FriendListBundle\Entity\User;
use Core\FriendListBundle\Form\Type\FriendGroupsType;
use Core\FriendListBundle\Form\Type\AddFriendsType;
use Core\FriendListBundle\Form\Type\ResearchFriendsType;
use Core\FriendListBundle\Form\Type\SelectGroupType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FriendsController extends FOSRestController
{
    /**
    * @return array
    * @View()
    */
    public function getFriendsAction($userId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);       
        $conn = $this->container->get('database_connection');

        $query = "SELECT fd.name, fd.friendgroup_id
                FROM friend fd
                inner join friendGroups fg on fg.id = fd.friendgroup_id
                inner join user u on u.id = fg.user_id
                where u.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $user->getId());
        $stmt->execute();

        return array('friends' => $stmt);
    }

    /**
    * @return array
    * @View()
    */
    public function postFriendsAction(Request $request, $friendId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user'=>$user,'name'=>'wait'));
        $form = $this->createForm(new AddFriendsType(), $friend = new Friend());
        $jsonPost = json_decode($request->getContent(), true);

        if ($request->isMethod('POST')) {
            $form->bind($jsonPost);
            if ($form->isValid()) {
                $receivingUser = $entityManager->getRepository('CoreUserBundle:User')->find($friendId);
                $friend->setFriendGroup($friendGroup);
                $friend->setName($receivingUser->getUsername());
                $friend->setSender($user);
                
                if(!$receivingUser)
                    throw $this->createNotFoundException('Friend' . $friendId . 'not exist');
                
                $friend->setFriend($receivingUser);
                $receivingfriend = new Friend();
                $receivingfriendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user'=>$receivingUser,'name'=>'wait'));
                $receivingfriend->setFriendGroup($receivingfriendGroup);
                $receivingfriend->setName($user->getUsername());
                $receivingfriend->setSender($user);
                $receivingfriend->setFriend($user);  
                $conn = $this->container->get('database_connection');

                $query = "SELECT fd.name, fd.friendgroup_id
                        FROM friend fd
                        inner join friendGroups fg on fg.id = fd.friendgroup_id
                        inner join user u on u.id = fg.user_id
                        where u.id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bindValue(1, $user->getId());
                $stmt->execute();

                foreach ($stmt as $f){
                    if($f['name'] == $receivingUser->getUsername())
                        return $this->redirect($this->generateUrl('core_friendList_homepage'));        
                }

                if($receivingUser->getUsername() != $user->getUsername()){
                    $entityManager->persist($friend);
                    $entityManager->persist($receivingfriend);
                    $entityManager->flush();
                }
                return array('code' => 200, 'text' => 'POST OK');
            }
        }
        return array ('code' => 400, $form);
    }

    /**
    * @return array
    * @View()
    */
    public function deleteFriendsAction($friendId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friend = $entityManager->getRepository('CoreFriendListBundle:Friend')->find($friendId);
        $conn = $this->container->get('database_connection');

        $query = "SELECT fd.id
                FROM friend fd
                WHERE (fd.name = :user AND fd.sender = :name)
                OR (fd.name = :user AND fd.sender = :user)";
        $stmt = $conn->prepare($query);
        $stmt->bindValue('name', $friend->getName());
        $stmt->bindValue('user', $user->getUsername());
        $stmt->execute();

        foreach ($stmt as $f) {
            $friendId = $f['id'];
        }

        $friendDelete = $entityManager->getRepository('CoreFriendListBundle:Friend')->find($friendId);
        if (!$friend) 
            throw $this->createNotFoundException('Friend Not Found');
        
        $entityManager->remove($friendDelete);
        $entityManager->remove($friend);
        $entityManager->flush();
        return array('code' => 200, 'text' => 'DELETE DONE');

    }

    /**
    * @return array
    * @View()
    */
    public function getBygroupfriendsAction($friendGroupId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();        
        $conn = $this->container->get('database_connection');
        $query = "SELECT fd.name, fd.friendgroup_id, fd.sender, fd.id
                FROM friend fd
                inner join friendGroups fg on fg.id = fd.friendgroup_id
                inner join user u on u.id = fg.user_id
                where u.id = ? and fg.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $user->getId());
        $stmt->bindValue(2, $friendGroupId);
        $stmt->execute();

        return array('friends' => $stmt);
    }

    /**
    * @return array
    * @View()
    */
    public function postValidfriendAction($friendId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user'=>$user,'name'=>'general'));
        $friend = $entityManager->getRepository('CoreFriendListBundle:Friend')->find($friendId);
        $senderUser = $entityManager->getRepository('CoreUserBundle:User')->findOneBy(array('username'=>$friend->getSender()));
        $senderGeneralGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user'=>$senderUser,'name'=>'general'));
        $senderFriend = $entityManager->getRepository('CoreFriendListBundle:Friend')->findOneBy(array('sender'=>$senderUser->getUsername(),'friend'=>$user->getId()));

        if($friend->getSender() != $user->getUsername()){
            $friend->setFriendGroup($friendGroup);
            $entityManager->persist($friend);

            $senderFriend->setFriendGroup($senderGeneralGroup);
            $entityManager->persist($senderFriend);

            $entityManager->flush();
            return array('code' => 200, 'text' => 'OK ON EST TROP AMI!');
        }
        return array('code' => 400, 'text' => 'Only receiver can accept');
    }

    /**
    * @return array
    * @View()
    */
    public function postFriendGroupAction(Request $request, $friendId, $friendGroupId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new SelectGroupType($user));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()){
                $friend = $entityManager->getRepository('CoreFriendListBundle:Friend')->find($friendId);
                $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->find($friendGroupId);
                $friend->setFriendGroup($friendGroup);
                $entityManager->persist($friend);
                $entityManager->flush();

                return array('code' => 200, 'text' => 'MOVE OK');
            }
        }
        return array('code' => 400, $form);
    }
}
