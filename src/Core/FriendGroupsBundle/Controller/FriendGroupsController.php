<?php

namespace Core\FriendGroupsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\FriendGroupsBundle\Entity;

class FriendGroupsController extends Controller
{

	public function addGroup($idFriendList, $name)
	{
		$FriendGroups = new FriendGroups();
		$FriendGroups->setFriendlistsId($idFriendList);
		$FriendGroups->setGroupsNames($name);
		$em = $this->getDoctrine()->getEntityManager();
        $em->persist($FriendLists);
        $em->flush();
	}

    public function affectFriend($idFriendlist, $idfriend)
    {

		$desk = $this->getDoctrine()->getRepository('FriendGroupsBundle:FriendGroups')->find($id);
        $FriendGroups->setFriendlistsId($idFriendlist);
       	
    }
}