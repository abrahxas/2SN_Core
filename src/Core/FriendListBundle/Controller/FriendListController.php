<?php

namespace Core\FriendListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Core\FriendListBundle\Entity\FriendGroups;
use Core\FriendListBundle\Entity\Friend;
use Core\FriendListBundle\Entity\User;
use Core\FriendListBundle\Form\Type\FriendGroupsType;
use Core\FriendListBundle\Form\Type\AddFriendsType;
use Core\FriendListBundle\Controller\FriendController;


class FriendListController extends Controller
{
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friendList = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->findBy(array('user' => $user));

        return $this->render('CoreFriendListBundle:default:index.html.twig', array(
            'friendLists' => $friendList
        ));
    }

    public function addAction(Request $request)
    {
    	$entityManager = $this->getDoctrine()->getManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$form = $this->createForm(new FriendGroupsType(), $friendGroups = new FriendGroups());

    	if($request->isMethod('POST')){
    		$form->handleRequest($request);
    		if($form->isValid()){
                if($this->GroupExist($form->get('name')->getData())){
                    return $this->render('CoreFriendListBundle:default:add.html.twig', array(
                        'form'=> $form->createView()
                    ));
                }
    			$friendGroups->setUser($user);
    			$entityManager->persist($friendGroups);
    			$entityManager->flush();

    			return $this->redirect($this->generateUrl('core_friendList_homepage'));
    		}
    	}
    	return $this->render('CoreFriendListBundle:default:add.html.twig', array(
    		'form'=> $form->createView()
		));
    }

    public function updateAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->find($request->get('friendGroupId'));
        $form = $this->createForm(new FriendGroupsType(), $friendGroup);

        if (!$friendGroup)
            throw $this->createNotFoundException('Friend Group Not Found');

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if($this->GroupExist($form->get('name')->getData())){
                    return $this->render('CoreFriendListBundle:default:add.html.twig', array(
                        'form' => $form->createView(),
                    ));
                }
                $friendGroup->setUser($user);
                $entityManager->persist($friendGroup);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('core_friendList_homepage'));
            }
        }
        return $this->render('CoreFriendListBundle:default:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $friendGroup = $entityManager->getRepository('CoreFriendListBundle:FriendGroups')->find($$request->get('friendGroupId'));
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

        return $this->redirect($this->generateUrl('core_friendList_homepage'));
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

}
