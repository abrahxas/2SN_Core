<?php

namespace Core\FriendListBundle\Controller;

use Core\FriendListBundle\Entity\FriendGroups;
use Core\FriendListBundle\Entity\Friend;
use Core\FriendListBundle\Entity\User;
use Core\FriendListBundle\Form\Type\AddFriendsType;
use Core\FriendListBundle\Form\Type\SelectGroupType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;

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
        $friends = $entityManager->getRepository('CoreFriendListBundle:Friend')->findby(array('friend'=> $user)); 

        return array('code' => 200, 'friends' => $friends);
    }

        /**
    * @return array
    * @View()
    */
    public function getUserFriendsbyfriendgroupAction($userId, $friendgroupId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $friendgroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->find($friendgroupId); 
        $friends = $entityManager->getRepository('CoreFriendListBundle:Friend')->findby(array('friend'=> $user, 'friendgroup' => $friendgroup)); 

        return array('code' => 200, 'friends' => $friends);
    }

    /**
    * @return array
    * @View()
    */
    public function postUserFriendsAction(Request $request,$userId, $newFriendId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $userWaitGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user' => $user, 'name' => 'wait'));
        $newFriend = $entityManager->getRepository('CoreUserBundle:User')->find($newFriendId);
        $newFriendWaitGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user' => $newFriend, 'name' => 'wait'));
        $friends = $entityManager->getRepository('CoreFriendListBundle:Friend')->findBy(array('friend'=> $user));

        $friend = new Friend();
        $friend->setFriendGroup($userWaitGroup);
        $friend->setSender($user->getUsername());
        $friend->setFriend($user);
        $friend->setUser($newFriend);

        $userInFriend = new Friend();
        $userInFriend->setFriendGroup($newFriendWaitGroup);
        $userInFriend->setSender($user->getUsername());
        $userInFriend->setFriend($newFriend);
        $userInFriend->setUser($user);

        foreach ($friends as $f) {
            if ($f->getUser()->getId() == $newFriend->getId() && $f->getFriend()->getId() == $user->getId()) {
                return array('code' => 400, 'data' => $newFriend->getUsername() . ' already exists');
            }
        }

        if ($newFriend->getUsername() != $user->getUsername()) {
            $userWaitGroup->addFriend($friend);
            $newFriendWaitGroup->addFriend($userInFriend);
            $entityManager->persist($friend);
            $entityManager->persist($userInFriend);
            $entityManager->flush(); 
        }

        return array('code' => 200, 'friend' => $friend);
    }

    /**
    * @return array
    * @View()
    */
    public function deleteUserFriendAction($userId, $friendId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $friend = $entityManager->getRepository('CoreFriendListBundle:Friend')->find($friendId);
        $userInFriend = $entityManager->getRepository('CoreFriendListBundle:Friend')->findOneBy(array('user' => $user, 'friend' => $friend->getUser()));

        $entityManager->remove($userInFriend);
        $entityManager->remove($friend);
        $entityManager->flush();

        return array('code' => 200,'data' => 'Delete done');
    }

    /**
    * @return array
    * @View()
    */
    public function postUserValidfriendAction($userId, $friendId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $userGeneralGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user' => $user, 'name' => 'general'));
        $friend = $entityManager->getRepository('CoreFriendListBundle:Friend')->find($friendId);
        $friendInUser = $entityManager->getRepository('CoreUserBundle:User')->find($friend->getUser()->getId());
        $friendGeneralGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user' => $friendInUser, 'name' => 'general'));
        $userInFriend = $entityManager->getRepository('CoreFriendListBundle:Friend')->findOneBy(array('user' => $user->getId(), 'friend' => $friendInUser->getId()));
        
        if ($friend->getSender() != $user->getUsername()) {
            $friend->setFriendGroup($userGeneralGroup);
            $userInFriend->setFriendGroup($friendGeneralGroup);
            $entityManager->persist($friend);
            $entityManager->persist($userInFriend);
            $entityManager->flush();

            return array('code' => 200, 'data' => $friend);
        }

        return array('code' => 400, 'data' => 'Only receiver can accept');
    }

    // /**
    // * @return array
    // * @View()
    // */
    // public function postFriendGroupAction(Request $request, $friendId, $friendGroupId)
    // {
    //     $entityManager = $this->getDoctrine()->getManager();
    //     $user = $this->container->get('security.context')->getToken()->getUser();
    //     $form = $this->createForm(new SelectGroupType($user));

    //     if ($request->isMethod('POST') && !empty($jsonPost)) {
    //         $form->handleRequest($request);
    //         if ($form->isSubmitted() && $form->isValid()) {
    //             $friend = $entityManager->getRepository('CoreFriendListBundle:Friend')->find($friendId);
    //             $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->find($friendGroupId);
    //             $friend->setFriendGroup($friendGroup);
    //             $entityManager->persist($friend);
    //             $entityManager->flush();

    //             return array(
    //                 'code' => 200,
    //                 'data' => $friend,
    //             );
    //         }
    //     }

    //     return array(
    //         'code' => 400,
    //         $form,
    //     );
    // }
}
