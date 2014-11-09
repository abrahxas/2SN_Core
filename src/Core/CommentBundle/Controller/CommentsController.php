<?php

namespace Core\CommentBundle\Controller;

use Core\CommentBundle\Entity\Comment;
use Core\CommentBundle\Form\Type\CommentType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentsController extends FOSRestController
{
  /**
    * @return array
    * @View()
    */
  public function getCommentsAction(Request $request)
  {
    $entityManager = $this->getDoctrine()->getManager();
    if ($request->get('postId') != null)
      $comments = $entityManager->getRepository('CoreCommentBundle:Comment')->findBy(array('post' => $request->get('postId')), array('createdAt' => 'DESC'));
    else
      $comments = $entityManager->getRepository('CoreCommentBundle:Comment')->findBy(array('photo' => $request->get('photoId')), array('createdAt' => 'DESC'));

    return array('comments' => $comments);
  }

  /**
    * @return array
    * @View()
    */
  public function getAdminCommentsAction()
  {
    $entityManager = $this->getDoctrine()->getManager();
    $comments = $entityManager->getRepository('CoreCommentBundle:Comment')->findAll();

    return array('comments' => $comments);
  }

  /**
    * @return array
    * @View()
    */
  public function postCommentsAction(Request $request)
  {
    $entityManager = $this->getDoctrine()->getManager();
    $user = $this->container->get('security.context')->getToken()->getUser();
    $form = $this->createForm(new CommentType(), $comment = new Comment());

    if ($request->get('postId') != null)
      $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($request->get('postId'));
    else
      $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($request->get('photoId'));

    $jsonPost = json_decode($request->getContent(), true);
    if ($request->isMethod('POST')) {
      $form->bind($jsonPost);
      if ($form->isValid()) {
        $comment->setUser($user);
        ($request->get('postId') != null) ? $comment->setPost($post) : $comment->setPhoto($photo);
        $entityManager->persist($comment);
        $entityManager->flush();

        if ($request->get('postId') != null)
          return array('code' => 200, 'text' => 'Post comment OK');
        else
          return array('code' => 200, 'text' => 'Photo comment OK');
      }
    }

    return array('code' => 400, $form);
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
      throw $this->createNotFoundException('Comment Not Found');
    }

    $jsonPost = json_decode($request->getContent(), true);
    if ($request->isMethod('PUT')) {
      $form->bind($jsonPost);
      if ($form->isValid()) {
        $comment->setUser($user);
        $comment->setUpdatedAt(new \DateTime());
        $entityManager->persist($comment);
        $entityManager->flush();

        return array('code' => 200, 'text' => 'Update comment OK');
      }
    }

    return array('code' => 400, $form);
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
      throw $this->createNotFoundException('Comment Not Found');
    }

    $entityManager->remove($comment);
    $entityManager->flush();

    return array('code' => 200, 'text' => 'Delete comment OK');
  }
}