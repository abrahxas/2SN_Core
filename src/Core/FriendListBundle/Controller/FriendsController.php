<?php

namespace Core\FriendListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Core\FriendListBundle\Entity\FriendGroups;
use Core\FriendListBundle\Entity\Friend;
use Core\FriendListBundle\Entity\User;
use Core\FriendListBundle\Form\Type\AddFriendsType;
use Core\FriendListBundle\Form\Type\SelectGroupType;

class FriendsController extends Controller
{
    /**
    * @return array
    * @View()
    */
    public function getFriendsAction($userId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $friends = $entityManager->getRepository('CoreFriendListBundle:Friend')->findBy(array('user' => $user));

        return array('code' => 200, 'friends' => $friends);
    }

    /**
    * @return array
    * @View()
    */
    public function postFriendsAction(Request $request, $userId, $friendId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user' => $user, 'name' => 'wait'));
        $form = $this->createForm(new AddFriendsType(), $friend = new Friend());
        $jsonPost = json_decode($request->getContent(), true);

        if ($request->isMethod('POST') && !empty($jsonPost)) {
            $form->bind($jsonPost);
            if ($form->isSubmitted() && $form->isValid()) {
                $newFriend = $entityManager->getRepository('CoreUserBundle:User')->find($friendId);
                $friend->setFriendGroup($friendGroup);
                $friend->setSender($user->getId());
                $friend->setFriend($newFriend);

                $currentUser = new Friend();
                $currentUserFriendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user' => $newFriend, 'name' => 'wait'));
                $currentUser->setFriendGroup($currentUserFriendGroup);
                $currentUser->setSender($user);
                $currentUser->setFriend($user);

                $friends = $entityManager->getRepository('CoreFriendListBundle:Friend')->findBy(array('user' => $user));
                foreach ($friends as $f) {
                    if ($f['id'] == $newFriend->getId()) {
                        return $this->redirect($this->generateUrl('core_friendList_homepage'));
                    }
                }
                if ($newFriend->getId() != $user->getId()) {
                    $entityManager->persist($friend);
                    $entityManager->persist($currentUser);
                    $entityManager->flush();
                }
                return array('code' => 200, 'data' => $friend);
            }
        }
        return array('code' => 400, $form);
    }

    /**
    * @return array
    * @View()
    */
    public function deleteFriendsAction($userId, $friendId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $friend = $entityManager->getRepository('CoreFriendListBundle:Friend')->find($friendId);
        $friendOfFriend = $entityManager->getRepository('CoreFriendListBundle:Friend')->findOneBy(array('user' => $user, 'sender' => $friend->getSender()));

        $entityManager->remove($friendOfFriend);
        $entityManager->remove($friend);
        $entityManager->flush();

        return array('code' => 200, 'data' => 'Delete done');
    }

    /**
    * @return array
    * @View()
    */
    public function getBygroupfriendsAction($userId, $friendGroupId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->find($friendGroupId);
        $friends = $entityManager->getRepository('CoreFriendListBundle:Friend')->findOneBy(array('user' => $user, 'friendgroup' => $friendGroup);

        return array('code' => 200, 'friends' => $friends);
    }

    /**
    * @return array
    * @View()
    */
    public function postValidfriendAction($userId, $friendId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $userGeneralGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user' => $user, 'name' => 'general'));
        $friend = $entityManager->getRepository('CoreFriendListBundle:Friend')->find($friendId);
        $sender = $entityManager->getRepository('CoreUserBundle:User')->findOneBy(array('username' => $friend->getSender()));
        $senderGeneralGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user' => $sender, 'name' => 'general'));
        $userFriend = $entityManager->getRepository('CoreFriendListBundle:Friend')->findOneBy(array('sender' => $sender->getUsername(), 'friend' => $user->getId()));

        if ($friend->getSender() != $user->getUsername()) {
            $friend->setFriendGroup($userGeneralGroup);
            $userFriend->setFriendGroup($senderGeneralGroup);
            $entityManager->persist($friend);
            $entityManager->persist($userFriend);
            $entityManager->flush();

            return array('code' => 200, 'data' => $friend);
        }

        return array('code' => 400, 'data' => 'Only receiver can accept');
    }

    /**
    * @return array
    * @View()
    */
    public function postFriendGroupAction(Request $request, $userId, $friendId, $friendGroupId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $form = $this->createForm(new SelectGroupType($user));

        if ($request->isMethod('POST') && !empty($jsonPost)) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $friend = $entityManager->getRepository('CoreFriendListBundle:Friend')->find($friendId);
                $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->find($friendGroupId);
                $friend->setFriendGroup($friendGroup);
                $entityManager->persist($friend);
                $entityManager->flush();

                return array('code' => 200, 'data' => $friend);
            }
        }
        return array('code' => 400, $form);
    }
}