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
    public function getUserMeAction()
    {
        $usr = $this->get('security.context')->getToken()->getUser();
        return array('user' => $usr);
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
                $url = "http://localhost:8888/2SN_Core/web/app_dev.php/api/login_check";
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

    public function putUserAction(Request $request, $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $em->getRepository('CoreUserBundle:User')->find($userId);

        $dispatcher = $this->container->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $formFactory = $this->container->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);
        $jsonPost = json_decode($request->getContent(), true);

        if ('PUT' === $request->getMethod()) {
            $form->bind($jsonPost);

            if ($form->isValid()) {
                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->container->get('fos_user.user_manager');

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('fos_user_profile_show');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return array('code' => 200 , 'data' => $user);
            }
        }
        return array ('code' => 400, $form);
    }


    public function deleteUserAction($userId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CoreUserBundle:User')->find($userId);
        if (!$user) {
            return array('code' => 404, 'data' => 'User not found');
        }
        $em->remove($user);
        $em->flush();
        return array('code' => 200, 'data' => 'Delete done');
    }
}

// { "email":"test@mail.com", "username":"test", "plainPassword":{"first":"test","second":"test"} }
