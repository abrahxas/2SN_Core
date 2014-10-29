<?php

namespace Core\UserBundle\Controller;

use Core\UserBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Lexik\Bundle\JWTAuthenticationBundle\Event;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * @Post("/register")
     */
	public function registerAction(Request $request)
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
                $username = $jsonPost['username'];
                $password = $jsonPost['plainPassword']['first'];
                $newUser = array(
                    'username' => $username,
                    'password' => $password
                    );
                $jsonLogin = json_encode($newUser);
	            $event = new FormEvent($form, $request);
	            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
	            $userManager->updateUser($user);
                $url = "http://localhost:8888/ETNA/2SN_Core/web/app_dev.php/api/login_check";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonLogin);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonLogin))
                );
                $result = curl_exec($ch);
	            $response = new Response($result);

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
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User Not Found');
        }
        $em->remove($user);
        $em->flush();
        $response = new Response("delete done", 200);
        return $response;
    }
}

// { "email":"test@mail.com", "username":"test", "plainPassword":{"first":"test","second":"test"} }
