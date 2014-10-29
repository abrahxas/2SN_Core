<?php

namespace Core\CommentBundle\Controller;

use Core\CommentBundle\Entity\Comment;
use Core\CommentBundle\Form\Type\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommentController extends Controller
{
  public function indexAction(Request $request)
  {
    $entityManager = $this->getDoctrine()->getManager();
    if ($request->get('postId') != null)
      $comments = $entityManager->getRepository('CoreCommentBundle:Comment')->findBy(array('post' => $request->get('postId')), array('createdAt' => 'DESC'));
    else
      $comments = $entityManager->getRepository('CoreCommentBundle:Comment')->findBy(array('photo' => $request->get('photoId')), array('createdAt' => 'DESC'));

    return $this->render('CoreCommentBundle:default:index.html.twig', array(
      'comments' => $comments
    ));
  }

  public function indexAllAction()
  {
    $entityManager = $this->getDoctrine()->getManager();
    $comments = $entityManager->getRepository('CoreCommentBundle:Comment')->findAll();

    return $this->render('CoreCommentBundle:default:indexAll.html.twig', array(
        'comments' => $comments
    ));
  }

  public function addAction(Request $request)
  {
    $entityManager = $this->getDoctrine()->getManager();
    $user = $this->container->get('security.context')->getToken()->getUser();
    $form = $this->createForm(new CommentType(), $comment = new Comment());

    if ($request->get('postId') != null)
      $post = $entityManager->getRepository('CoreBlogBundle:Post')->find($request->get('postId'));
    else
      $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($request->get('photoId'));

    if ($request->isMethod('POST')) {
      $form->handleRequest($request);
      if ($form->isValid()) {
        $comment->setUser($user);
        ($request->get('postId') != null) ? $comment->setPost($post) : $comment->setPhoto($photo);
        $entityManager->persist($comment);
        $entityManager->flush();

        if ($request->get('postId') != null)
          return $this->redirect($this->generateUrl('core_comment_blog_homepage', array('postId' => $request->get('postId'))));
        else
          return $this->redirect($this->generateUrl('core_comment_photo_homepage', array('photoId' => $request->get('photoId'))));
      }
    }

    return $this->render('CoreCommentBundle:default:create.html.twig', array(
      'form' => $form->createView(),
    ));
  }

  public function updateAction(Request $request)
  {
    $entityManager = $this->getDoctrine()->getManager();
    $user = $this->container->get('security.context')->getToken()->getUser();
    $comment = $entityManager->getRepository('CoreCommentBundle:Comment')->find($request->get('id'));
    $form = $this->createForm(new CommentType(), $comment);

    if (!$comment) {
      throw $this->createNotFoundException('Comment Not Found');
    }

    if ($request->isMethod('POST')) {
      $form->handleRequest($request);
      if ($form->isValid()) {
        $comment->setUser($user);
        $comment->setUpdatedAt(new \DateTime());
        $entityManager->persist($comment);
        $entityManager->flush();

        if ($request->get('postId') != null)
          return $this->redirect($this->generateUrl('core_comment_blog_homepage', array('postId' => $request->get('postId'))));
        elseif($request->get('photoId') != null)
          return $this->redirect($this->generateUrl('core_comment_photo_homepage', array('photoId' => $request->get('photoId'))));
        else
          return $this->redirect($this->generateUrl('core_comment_all'));

      }
    }

    return $this->render('CoreCommentBundle:default:update.html.twig', array(
      'form' => $form->createView(),
    ));
  }

  public function deleteAction(Request $request)
  {
    $entityManager = $this->getDoctrine()->getManager();
    $comment = $entityManager->getRepository('CoreCommentBundle:Comment')->find($request->get('id'));

    if (!$comment) {
      throw $this->createNotFoundException('Comment Not Found');
    }

    $entityManager->remove($comment);
    $entityManager->flush();

    if ($request->get('postId') != null)
      return $this->redirect($this->generateUrl('core_comment_blog_homepage', array('postId' => $request->get('postId'))));
    elseif ($request->get('photoId') != null)
      return $this->redirect($this->generateUrl('core_comment_photo_homepage', array('photoId' => $request->get('photoId'))));
    else
      return $this->redirect($this->generateUrl('core_comment_all'));
  }
}
