<?php

namespace Core\CommentBundle\Controller;

use Core\CommentBundle\Entity\Comment;
use Core\CommentBundle\Form\Type\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommentController extends Controller
{
  public function indexAction($postId = null, $photoId = null)
  {
    $entityManager = $this->getDoctrine()->getManager();
    if ($postId != null)
      $comments = $entityManager->getRepository('CoreCommentBundle:Comment')->findBy(array('post' => $postId), array('createdAt' => 'DESC'));
    else
      $comments = $entityManager->getRepository('CoreCommentBundle:Comment')->findBy(array('photo' => $photoId), array('createdAt' => 'DESC'));

    return $this->render('CoreCommentBundle:default:index.html.twig', array(
      'comments' => $comments
    ));
  }

  public function addAction($postId = null, $photoId = null, Request $request)
  {
    $entityManager = $this->getDoctrine()->getManager();
    $user = $this->container->get('security.context')->getToken()->getUser();
    $form = $this->createForm(new CommentType(), $comment = new Comment());
    if ($postId != null)
      $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($postId);
    else
      $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($photoId);

    if ($request->isMethod('POST')) {
      $form->handleRequest($request);
      if ($form->isValid()) {
        $comment->setUser($user);
        ($postId != null) ? $comment->setPost($post) : $comment->setPhoto($photo);
        $entityManager->persist($comment);
        $entityManager->flush();

        if ($postId != null)
          return $this->redirect($this->generateUrl('core_comment_blog_homepage', array('postId' => $post->getId())));
        else
          return $this->redirect($this->generateUrl('core_comment_photo_homepage', array('photoId' => $photo->getId())));
      }
    }

    return $this->render('CoreCommentBundle:default:create.html.twig', array(
      'form' => $form->createView(),
    ));
  }

  public function updateAction($postId = null , $photoId = null, $id, Request $request)
  {
    $entityManager = $this->getDoctrine()->getManager();
    $user = $this->container->get('security.context')->getToken()->getUser();
    $comment = $entityManager->getRepository('CoreCommentBundle:Comment')->find($id);
    $form = $this->createForm(new CommentType(), $comment);
    if ($postId != null)
      $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($postId);
    else
      $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($photoId);

    if (!$comment) {
      throw $this->createNotFoundException('Comment Not Found');
    }

    if ($request->isMethod('POST')) {
      $form->handleRequest($request);
      if ($form->isValid()) {
        $comment->setUser($user);
        ($postId != null) ? $comment->setPost($post) : $comment->setPhoto($photo);
        $comment->setUpdatedAt(new \DateTime());
        $entityManager->persist($comment);
        $entityManager->flush();

        if ($postId != null)
          return $this->redirect($this->generateUrl('core_comment_blog_homepage', array('postId' => $post->getId())));
        else
          return $this->redirect($this->generateUrl('core_comment_photo_homepage', array('photoId' => $photo->getId())));
      }
    }

    return $this->render('CoreCommentBundle:default:update.html.twig', array(
      'form' => $form->createView(),
    ));
  }

  public function deleteAction($postId = null, $photoId = null, $id)
  {
    $entityManager = $this->getDoctrine()->getManager();
    $comment = $entityManager->getRepository('CoreCommentBundle:Comment')->find($id);

    if (!$comment) {
      throw $this->createNotFoundException('Comment Not Found');
    }

    $entityManager->remove($comment);
    $entityManager->flush();

    if ($postId != null)
      return $this->redirect($this->generateUrl('core_comment_blog_homepage', array('postId' => $postId)));
    else
      return $this->redirect($this->generateUrl('core_comment_photo_homepage', array('photoId' => $photoId)));
  }
}
