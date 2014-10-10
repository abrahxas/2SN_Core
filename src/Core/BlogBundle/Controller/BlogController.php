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
        $user = $this->container->get('security.context')->getToken()->getUser();
        $posts = $entityManager->getRepository('CoreBlogBundle:Post')->findBy(array('user' => $user));

        return $this->render('CoreBlogBundle:default:index.html.twig', array(
            'posts' => $posts
        ));
    }

    public function addPostAction(Request $request)
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

        return $this->render('CoreBlogBundle:default:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function showPostAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Post Not Found');
        }

        return $this->render('CoreBlogBundle:default:show.html.twig', array(
            'post' => $post
        ));
    }

    public function updatePostAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($request->get('id'));
        $form = $this->createForm(new PostType(), $post);

        if (!$post) {
            throw $this->createNotFoundException('Post Not Found');
        }

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $post->setUser($user);
                $post->setUpdatedAt(new \DateTime());
                $entityManager->persist($post);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('core_blog_homepage'));
            }
        }

        return $this->render('CoreBlogBundle:default:update.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deletePostAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Post Not Found');
        }

        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('core_blog_homepage'));
    }
}
