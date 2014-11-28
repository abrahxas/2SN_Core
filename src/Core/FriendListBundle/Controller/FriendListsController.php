<?php

namespace Core\FriendListBundle\Controller;

use Core\FriendListBundle\Entity\FriendGroups;
use Core\FriendListBundle\Entity\Friend;
use Core\FriendListBundle\Entity\User;
use Core\FriendListBundle\Form\Type\FriendGroupsType;
use Core\FriendListBundle\Form\Type\AddFriendsType;
use Core\FriendListBundle\Controller\FriendController;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class FriendListsController extends FOSRestController
{
    /**
    * @return array
    * @View()
    */
    public function getFriendlistsAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friendLists = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findBy(array('user' => $user));

        return array('friendLists' => $friendLists);
    }

    /**
    * @return array
    * @View()
    */
    public function postFriendlistsAction(Request $request)
    {
    	$entityManager = $this->getDoctrine()->getManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$form = $this->createForm(new FriendGroupsType(), $friendGroups = new FriendGroups());
        $jsonPost = json_decode($request->getContent(), true);
    	if($request->isMethod('POST')){
    		$form->bind($jsonPost);
    		if($form->isValid()){
                if($this->GroupExist($form->get('name')->getData())){
                    return array('code' => 400, 'message' => 'Group already exist!');
                }
    			$friendGroups->setUser($user);
    			$entityManager->persist($friendGroups);
    			$entityManager->flush();

                return array('code' => 200, 'text' => 'POST OK');
    		}
    	}
        return array('code' => 400, $form);
    }

    /**
    * @return array
    * @View()
    */
    public function putFriendlistsAction(Request $request, $friendGroupId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->find($friendGroupId);
        $form = $this->createForm(new FriendGroupsType(), $friendGroup);

        if (!$friendGroup)
            throw $this->createNotFoundException('Friend Group Not Found');
        $jsonPost = json_decode($request->getContent(), true);
        if ($request->isMethod('PUT')) {
            $form->bind($jsonPost);
            if ($form->isValid()) {
                if($this->GroupExist($form->get('name')->getData())){
                    return array('code' => 400, 'message' => 'Group already exist!');
                }
                $friendGroup->setUser($user);
                $entityManager->persist($friendGroup);
                $entityManager->flush();
                return array('code' => 200, 'text' => 'PUT OK');
            }
        }
        return array('code' => 400, $form);
    }

    /**
    * @return array
    * @View()
    */
    public function deleteFriendlistsAction($friendGroupId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->find($friendGroupId);
        $moveGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user'=>$user,'name'=>'general'));
        $friendListmove = $entityManager->getRepository('CoreFriendListBundle:Friend')->find($friendGroupId);

        if (!$friendGroup)
            throw $this->createNotFoundException('Friend Group Not Found');

        if($friendGroup->getName() == 'wait' || $friendGroup->getName() == 'general')
            return ('code' => 400, 'text' => 'NOPE');

        foreach ($friendListmove as $friend){
            $this->postFriendMoveAction($friend->getId(), $moveGroup->getId());
        }
        $entityManager->remove($friendGroup);
        $entityManager->flush();

        return array('code' => 200, 'text' => 'DELETE OK');
    }

    /**
    * @return array
    * @View()
    */
    public function GroupExist($nameGroup)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user'=>$user,'name'=>$nameGroup));

        if(!$friendGroup)
            return false;
        else
            return true;
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

        return array('code' => 200, 'text' => 'MOVE OK');
    }

}
