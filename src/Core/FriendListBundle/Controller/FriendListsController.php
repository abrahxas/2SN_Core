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
    public function getFriendListsAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friendLists = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findBy(array('user' => $user));

        return array('friendLists' => $friendLists);
    }

    public function postFriendListsAction(Request $request)
    {
    	$entityManager = $this->getDoctrine()->getManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$form = $this->createForm(new FriendGroupsType(), $friendGroups = new FriendGroups());
        $jsonPost = json_decode($request->getContent(), true);
    	if($request->isMethod('POST')){
    		$form->bind($jsonPost);
    		if($form->isValid()){
                if($this->GroupExist($form->get('name')->getData())){
                    return $this->render('CoreFriendListBundle:default:add.html.twig', array(
                        'form'=> $form->createView()
                    ));
                }
    			$friendGroups->setUser($user);
    			$entityManager->persist($friendGroups);
    			$entityManager->flush();

                return array('code' => 200, 'text' => 'POST OK');
    		}
    	}
        return array('code' => 400, $form);
    }

    public function putFriendListsAction(Request $request, friendGroupId)
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
                    return $this->render('CoreFriendListBundle:default:add.html.twig', array(
                        'form' => $form->createView(),
                    ));
                }
                $friendGroup->setUser($user);
                $entityManager->persist($friendGroup);
                $entityManager->flush();
                return array('code' => 200, 'text' => 'PUT OK');
            }
        }
        return array('code' => 400, $form);
    }

    public function deleteFriendListsAction($friendGroupId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->find($friendGroupId);
        $mooveGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findOneBy(array('user'=>$user,'name'=>'general'));
        $friendListmoove = $entityManager->getRepository('CoreFriendListBundle:Friend')->findBy(array('friendgroup'=>$$request->get('friendGroupId')));

        if (!$friendGroup)
            throw $this->createNotFoundException('Friend Group Not Found');

        if($friendGroup->getName() == 'wait' || $friendGroup->getName() == 'general')
            return $this->redirect($this->generateUrl('core_friendList_homepage'));

        foreach ($friendListmoove as $friend){
            $this->mooveFriendInGroupAction($friend->getId(), $mooveGroup->getId());
        }
        $entityManager->remove($friendGroup);
        $entityManager->flush();

        return array('code' => 200, 'text' => 'DELETE OK');
    }

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

    public function moveFriendInGroupAction($friendId, $friendGroupId)
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

}
