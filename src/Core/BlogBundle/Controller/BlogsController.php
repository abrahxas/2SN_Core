<?php

namespace Core\BlogBundle\Controller;

use Core\BlogBundle\Entity\Post;
use Core\BlogBundle\Form\Type\PostType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;

class BlogsController extends FOSRestController
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

        return array(
            'posts' => $posts,
        );
    }

    /**
    * @return array
    * @View()
    */
    public function getAdminBlogsAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $posts = $entityManager->getRepository('CoreBlogBundle:Post')->findAll();

        return array(
            'posts' => $posts,
        );
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

        if ($request->isMethod('POST') && !empty($jsonPost)) {
            $form->bind($jsonPost);
            if ($form->isSubmitted() && $form->isValid()) {
                $post->setUser($user);
                $entityManager->persist($post);
                $entityManager->flush();

                return array(
                    'code' => 201,
                    'data' => $post,
                );
            }
        }

        return array(
            'code' => 400,
            $form,
        );
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
            return array(
                'code' => 404,
                'data' => 'Post not found',
            );
        }

        return array(
            'post' => $post,
        );
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
            return array(
                'code' => 404,
                'data' => 'Post not found',
            );
        }
        $jsonPost = json_decode($request->getContent(), true);
        if ($request->isMethod('PUT') && !empty($jsonPost)) {
            $form->bind($jsonPost);
            if ($form->isSubmitted() && $form->isValid()) {
                $post->setUser($user);
                $post->setUpdatedAt(new \DateTime());
                $entityManager->persist($post);
                $entityManager->flush();

                return array(
                    'code' => 200,
                    'data' => $post,
                );
            }
        }

        return array(
            'code' => 400,
            $form,
        );
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
            return array(
                'code' => 404,
                'data' => 'Post not foud',
            );
        }

        $entityManager->remove($post);
        $entityManager->flush();

        return array(
            'code' => 200,
            'data' => 'Delete done',
        );
    }
}
