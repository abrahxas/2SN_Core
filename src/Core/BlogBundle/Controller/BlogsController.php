<?php

namespace Core\BlogBundle\Controller;

use Core\BlogBundle\Entity\Post;
use Core\BlogBundle\Form\Type\PostType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


Class BlogsController extends FOSRestController
{
    /**
    * @return array
    * @View()
    */
    public function getBlogsAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $posts = $entityManager->getRepository('CoreBlogBundle:Post')->findBy(array('user' => $user));
        return array('posts' => $posts);
    }

    /**
    * @return array
    * @View()
    */
    public function getAdminBlogsAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $posts = $entityManager->getRepository('CoreBlogBundle:Post')->findAll();

        return array('posts' => $posts);
    }

    /**
     * @return array
     * @View()
     */
    public function postBlogsAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new PostType(), $post = new Post());
        $jsonPost = json_decode($request->getContent(), true);

        if ($request->isMethod('POST')){
            $form->bind($jsonPost);
            if ($form->isValid()){
                $post->setUser($user);
                $entityManager->persist($post);
                $entityManager->flush();
                return 'OK';
            }
        }
        return array(400, $form);
    }

    /**
     * @return array
     * @View()
     */
    public function getBlogAction($postId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($postId);

        if (!$post) {
            throw $this->createNotFoundException('Post Not Found');
        }

        return array('post' => $post);
    }

     /**
     * @return array
     * @View()
     */
    public function putBlogsAction(Request $request, $postId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($postId);
        $form = $this->createForm(new PostType(), $post);

        if (!$post) {
            throw $this->createNotFoundException('Post Not Found');
        }
        $jsonPost = json_decode($request->getContent(), true);
        if ($request->isMethod('PUT')) {
            $form->bind($jsonPost);
            if ($form->isValid()) {
                $post->setUser($user);
                $post->setUpdatedAt(new \DateTime());
                $entityManager->persist($post);
                $entityManager->flush();
                return 'Update OK';
            }
        }

        return 'Error';
    }

    /**
     * @return array
     * @View()
     */
    public function deleteBlogsAction($postId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($postId);

        if (!$post) {
            throw $this->createNotFoundException('Post Not Found');
        }

        $entityManager->remove($post);
        $entityManager->flush();

        return array('reponse' => 'Delete done!');
    }
}
