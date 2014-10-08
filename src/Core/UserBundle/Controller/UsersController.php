<?php

namespace Core\UserBundle\Controller;

use Core\UserBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\View\View as ViewV;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    /**
     * @return array
     * @View()
     */
	public function postUserAction(Request $request)
    {
	    $formFactory = $this->container->get('fos_user.registration.form.factory');
	    $userManager = $this->container->get('fos_user.user_manager');
	    $dispatcher = $this->container->get('event_dispatcher');

	    $user = $userManager->createUser();
	    $user->setEnabled(true);

	    $event = new GetResponseUserEvent($user, $request);
	    $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

	    if (null !== $event->getResponse()) {
	        return $event->getResponse();
	    }

	    $form = $formFactory->createForm();
	    $form->setData($user);
	    $jsonPost = json_decode($request->getContent(), true);
	    if ('POST' === $request->getMethod()) {
            $form->bind($jsonPost);
	        if ($form->isValid()) {
	            $event = new FormEvent($form, $request);
	            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
	            $userManager->updateUser($user);
	            $response = new Response("Yeah registration done", 201);
	            return $response;
	        }
	    }
        return array(400, $form);
	}

    public function putUserAction(Request $request, User $user)
    {
    }

    public function deleteUserAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        // @todo user exist
        $response = new Response("delete done", 200);
        return $response;
    }
}

// { "email":"test@mail.com", "username":"test", "plainPassword":{"first":"test","second":"test"} }