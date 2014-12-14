<?php

namespace Core\FriendListBundle\Controller;

use Core\FriendListBundle\Entity\FriendGroups;
use Core\FriendListBundle\Entity\Friend;
use Core\FriendListBundle\Entity\User;
use Core\FriendListBundle\Form\Type\FriendGroupsType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;

class FriendListsController extends FOSRestController
{
    /**
    * @return array
    * @View()
    */
    public function getFriendlistsAction($userId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $friendLists = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findBy(array('user' => $user));

        return array('code' => 200, 'friendLists' => $friendLists);
    }

    /**
    * @return array
    * @View()
    */
    public function postFriendlistsAction(Request $request, $userId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $form = $this->createForm(new FriendGroupsType(), $friendGroups = new FriendGroups());
        $jsonPost = json_decode($request->getContent(), true);
        if ($request->isMethod('POST') && !empty($jsonPost)) {
            $form->bind($jsonPost);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($this->GroupExist($form->get('name')->getData())) {
                    return array('code' => 400, 'data' => 'GROUP ALREADY EXIST');
                }
                $friendGroups->setUser($user);
                $entityManager->persist($friendGroups);
                $entityManager->flush();

                return array('code' => 200, 'data' => $friendGroups);
            }
        }

        return array('code' => 400, $form);
    }

    /**
    * @return array
    * @View()
    */
    public function putUserFriendlistsAction(Request $request, $userId, $friendGroupId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->find($friendGroupId);
        $form = $this->createForm(new FriendGroupsType(), $friendGroup);

        if (!$friendGroup) {
            return array('code' => 404, 'data' => 'Friendgroup ' . $friendGroupId . ' not found');
        }

        $jsonPost = json_decode($request->getContent(), true);
        if ($request->isMethod('PUT') && !empty($jsonPost)) {
            $form->bind($jsonPost);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($this->GroupExist($form->get('name')->getData())) {
                    return array('code' => 400, 'data' => 'Group already exist');
                }
                $friendGroup->setUser($user);
                $entityManager->persist($friendGroup);
                $entityManager->flush();

                return array('code' => 200, 'data' => $friendGroup);
            }
        }

        return array('code' => 400, $form);
    }

    /**
    * @return array
    * @View()
    */
    public function deleteUserFriendlistsAction($userId, $friendGroupId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->find($friendGroupId);
        $generalGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user' => $user, 'name' => 'general'));
        $friends = $entityManager->getRepository('CoreFriendListBundle:Friend')->findBy(array('friendgroup' => $friendGroup));

        if (!$friendGroup) {
            return array('code' => 404,'data' => 'Friengroup not found');
        }
        if ($friendGroup->getName() == 'wait' || $friendGroup->getName() == 'general') {
            return array('code' => 400, 'data' => 'Can\'t delete wait or general');
        }

        foreach ($friends as $friend) {
            $this->postFriendMoveAction($friend->getId(), $generalGroup->getId());
        }
        $entityManager->remove($friendGroup);
        $entityManager->flush();

        return array('code' => 200, 'data' => 'Delete done');
    }

    public function GroupExist($nameGroup)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user' => $user, 'name' => $nameGroup));

        if (!$friendGroup) {
            return false;
        } else {
            return true;
        }
    }

    /**
    * @return array
    * @View()
    */
    public function postFriendMoveAction($friendId, $friendGroupId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friend = $entityManager->getRepository('CoreFriendListBundle:Friend')->find($friendId);
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->find($friendGroupId);
        $friend->setFriendGroup($friendGroup);
        $entityManager->persist($friend);
        $entityManager->flush();

        return array(
            'code' => 200,
            'data' => $friendGroup,
        );
    }
}
