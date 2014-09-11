<?php

namespace Core\BlogBundle\Controller;

use Core\BlogBundle\Entity\Post;
use Core\BlogBundle\Form\Type\PostType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $posts = $entityManager->getRepository('CoreBlogBundle:Post')->findAll();

        return $this->render('CoreBlogBundle:Frontend:index.html.twig', array(
            'posts' => $posts
        ));
    }

    public function addAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new PostType(), $post = new Post());

        if ($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isValid()){
                $post->setUser($user);
                $entityManager->persist($post);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('core_blog_homepage'));
            }
        }

        return $this->render('CoreBlogBundle:backend:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function showAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($id);

        return $this->render('CoreBlogBundle:frontend:show.html.twig', array(
            'post' => $post
        ));
    }

    public function updateAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($request->get('id'));
        $form = $this->createForm(new PostType(), $post);

        if ($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isValid()){
                $post->setUser($user);
                $entityManager->persist($post);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('core_blog_homepage'));
            }
        }

        return $this->render('CoreBlogBundle:backend:update.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($id);
        if ($post)
        {
            $entityManager->remove($post);
            $entityManager->flush();
        }
        return $this->redirect($this->generateUrl('core_blog_homepage'));
    }
}
