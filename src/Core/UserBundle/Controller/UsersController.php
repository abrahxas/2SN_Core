<?php

namespace Core\UserBundle\Controller;

use Core\UserBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

Class UsersController extends FOSRestController
{

	/**
	 * @return array
	 * @View()
	 */
	public function getUsersAction()
	{
		$users = $this->getDoctrine()->getRepository('CoreUserBundle:User')->findAll();
		return array('users' => $users);
	}

	/**
	 * @param User $user
	 * @return array
	 * @View()
	 * @ParamConverter("user", class="CoreUserBundle:User")
	 */
	public function getUserAction(User $user)
	{
		return array('user' => $user);
	}
}