<?php

namespace Core\FriendListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Core\FriendListBundle\Entity\FriendGroups;
use Core\FriendListBundle\Entity\Friend;
use Core\FriendListBundle\Entity\User;
use Core\FriendListBundle\Form\Type\FriendGroupsType;
use Core\FriendListBundle\Form\Type\AddFriendsType;
use Core\FriendListBundle\Form\Type\ResearchFriendsType;

class FriendController extends Controller
{
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();        
        $conn = $this->container->get('database_connection');

        $query = "SELECT fd.name, fd.friendgroup_id
                FROM friend fd
                inner join friendGroups fg on fg.id = fd.friendgroup_id
                inner join user u on u.id = fg.user_id
                where u.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $user->getId());
        $stmt->execute();

        return $this->render('CoreFriendListBundle:default:indexFriend.html.twig', array(
            'friends' => $stmt
        ));
    }

    public function indexByGroupAction($friendGroupName)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();        
        $conn = $this->container->get('database_connection');

        $query = "SELECT fd.name, fd.friendgroup_id, fd.sender, fd.id
                FROM friend fd
                inner join friendGroups fg on fg.id = fd.friendgroup_id
                inner join user u on u.id = fg.user_id
                where u.id = ? and fg.name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $user->getId());
        $stmt->bindValue(2, $friendGroupName);
        $stmt->execute();

        if ($friendGroupName == 'wait')
        {
            return $this->render('CoreFriendListBundle:default:indexFriend.html.twig', array(
            'friends' => $stmt
            ));
        }else{
            return $this->render('CoreFriendListBundle:default:moove.html.twig', array(
            'friends' => $stmt
            ));
        } 
    }

    public function validAction($friendId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user'=>$user,'name'=>'general'));

        $friend = $entityManager->getRepository('CoreFriendListBundle:Friend')->find($friendId);

        $senderUser = $entityManager->getRepository('CoreUserBundle:User')->findOneBy(array('username'=>$friend->getSender()));
        $senderGeneralGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user'=>$senderUser,'name'=>'general'));

        $senderFriend = $entityManager->getRepository('CoreFriendListBundle:Friend')->findOneBy(array('sender'=>$senderUser->getUsername(),'friend'=>$user->getId()));

        if($friend->getSender() != $user->getUsername())
        {
            $friend->setFriendGroup($friendGroup);
            $entityManager->persist($friend);

            $senderFriend->setFriendGroup($senderGeneralGroup);
            $entityManager->persist($senderFriend);

            $entityManager->flush();
        }
        return $this->redirect($this->generateUrl('core_friendList_homepage'));
    }

    public function mooveFriendInGroupAction($friendId, $friendGroupId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friend = $entityManager->getRepository('CoreFriendListBundle:Friend')->find($friendId);
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->find($friendGroupId);

        $friend->setFriendGroup($friendGroup);

        $entityManager->persist($friend);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('core_friendList_homepage'));
    }


    public function addAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user'=>$user,'name'=>'wait'));
        $form = $this->createForm(new AddFriendsType(), $friend = new Friend());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {

                $friend->setFriendGroup($friendGroup);
                $friend->setSender($user);
                $receivingUser = $entityManager->getRepository('CoreUserBundle:User')->findOneBy(array('username'=>$form->get('name')->getdata()));
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

                foreach ($stmt as $f)
                {
                    if($f['name'] == $receivingUser->getUsername())
                    {
                        return $this->redirect($this->generateUrl('core_friendList_homepage'));
                    }
                }

                if($receivingUser->getUsername() != $user->getUsername())
                {
                    $entityManager->persist($friend);
                    $entityManager->persist($receivingfriend);
                    $entityManager->flush();
                }

                return $this->redirect($this->generateUrl('core_friendList_homepage'));
            }
        }

        return $this->render('CoreFriendListBundle:default:addfriends.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteAction($friendId)
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
        {
            throw $this->createNotFoundException('Friend Not Found');
        }

        $entityManager->remove($friendDelete);
        $entityManager->remove($friend);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('core_friendList_homepage'));
    }

    public function userResearchAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();  
        $conn = $this->container->get('database_connection');
        $form = $this->createForm(new ResearchFriendsType());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) 
            {
                $query = "SELECT us.username
                FROM user us
                WHERE us.username LIKE :username ";
                $stmt = $conn->prepare($query);
                $stmt->bindValue('username', '%'.$form->get('username')->getData().'%');
                $stmt->execute();

                return $this->render('CoreFriendListBundle:default:research.html.twig', array(
                'form' => $form->createView(),
                'friends'=>$stmt
            ));
            }
        }
        
        return $this->render('CoreFriendListBundle:default:research.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
