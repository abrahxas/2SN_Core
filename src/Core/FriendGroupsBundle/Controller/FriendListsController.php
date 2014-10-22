<?php

namespace Core\FriendGroupsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\FriendGroupsBundle\Entity;

class FriendListsController extends Controller
{
    public function AddList()
    {
    	
        $FriendLists = new FriendLists();
        $FriendLists->setUsersId($currentUsersId);
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($FriendLists);
        $em->flush();
    }
}