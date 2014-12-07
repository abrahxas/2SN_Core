<?php

namespace Core\CommentBundle\Controller;

use Core\CommentBundle\Entity\Comment;
use Core\CommentBundle\Form\Type\CommentType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;

class CommentsController extends FOSRestController
{
    /**
    * @return array
    * @View()
    */
    public function getCommentsAction($postId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comments = $entityManager->getRepository('CoreCommentBundle:Comment')->findBy(array('post' => $postId), array('createdAt' => 'DESC'));

        return array(
            'comments' => $comments,
        );
    }

    /**
    * @return array
    * @View()
    */
    public function getAdminCommentsAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comments = $entityManager->getRepository('CoreCommentBundle:Comment')->findAll();

        return array(
            'comments' => $comments,
        );
    }

    /**
    * @return array
    * @View()
    */
    public function postCommentsAction(Request $request, $postId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new CommentType(), $comment = new Comment());

        $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($postId);

        $jsonPost = json_decode($request->getContent(), true);
        if ($request->isMethod('POST') && !empty($jsonPost)) {
            $form->bind($jsonPost);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setUser($user);
                $comment->setPost($post);
                $entityManager->persist($comment);
                $entityManager->flush();

                return array(
                    'code' => 200,
                    'data' => $comment,
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
    public function putCommentsAction(Request $request, $commentId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $comment = $entityManager->getRepository('CoreCommentBundle:Comment')->find($commentId);
        $form = $this->createForm(new CommentType(), $comment);

        if (!$comment) {
            return array('code' => 404, 'data' => 'Comment not found');
        }

        $jsonPost = json_decode($request->getContent(), true);
        if ($request->isMethod('PUT') && !empty($jsonPost)) {
            $form->bind($jsonPost);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setUser($user);
                $comment->setUpdatedAt(new \DateTime());
                $entityManager->persist($comment);
                $entityManager->flush();

                return array(
                    'code' => 200,
                    'data' => $comment,
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
    public function deleteCommentsAction($commentId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comment = $entityManager->getRepository('CoreCommentBundle:Comment')->find($commentId);

        if (!$comment) {
            return array(
                'code' => 404,
                'data' => 'Comment not found',
            );
        }

        $entityManager->remove($comment);
        $entityManager->flush();

        return array(
            'code' => 200,
            'text' => 'Delete done',
        );
    }
}
