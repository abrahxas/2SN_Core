<?php

namespace Core\BlogBundle\Controller;

use Core\BlogBundle\Entity\Comment;
use Core\BlogBundle\Form\Type\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommentController extends Controller
{
    public function indexAction($postId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comments = $entityManager->getRepository('CoreBlogBundle:Comment')->findBy(array('post' => $postId), array('createdAt' => 'DESC'));

        return $this->render('CoreBlogBundle:Frontend:indexComment.html.twig', array(
            'postId' => $postId,
            'comments' => $comments
        ));
    }

    public function updateAction($postId, $id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($postId);
        $comment = $entityManager->getRepository('CoreBlogBundle:Comment')->find($id);
        $form = $this->createForm(new CommentType(), $comment);

        if ($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isValid()){
                $comment->setUser($user);
                $comment->setPost($post);
                $comment->setUpdatedAt(new \DateTime());
                $entityManager->persist($comment);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('core_blog_homepage_show', array('id' => $postId)));
            }
        }

        return $this->render('CoreBlogBundle:backend:updateComment.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteAction($postId, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comment = $entityManager->getRepository('CoreBlogBundle:Comment')->find($id);
        if ($comment)
        {
            $entityManager->remove($comment);
            $entityManager->flush();
        }
        return $this->redirect($this->generateUrl('core_blog_homepage_show', array('id' => $postId)));
    }
}
