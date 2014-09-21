<?php

namespace Core\GalleryBundle\Controller;

use Core\GalleryBundle\Entity\CommentPhoto;
use Core\GalleryBundle\Form\Type\CommentPhotoType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommentPhotoController extends Controller
{
    public function indexAction($albumSlug, $photoSlug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->findOneBy(array('slug' => $photoSlug));
        $comments = $entityManager->getRepository('CoreGalleryBundle:CommentPhoto')->findBy(array('photo' => $photo), array('createdAt' => 'DESC'));

        return $this->render('CoreGalleryBundle:default:indexCommentPhoto.html.twig', array(
            'albumSlug' => $albumSlug,
            'photoSlug' => $photoSlug,
            'comments' => $comments
        ));
    }

    public function addAction($albumSlug, $photoSlug, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->findOneBy(array('slug' => $photoSlug));
        $form = $this->createForm(new CommentPhotoType(), $comment = new CommentPhoto());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $comment->setUser($user);
                $comment->setPhoto($photo);
                $entityManager->persist($comment);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('core_photo_show', array('albumSlug' => $albumSlug,'photoSlug' => $photoSlug)));
            }
        }

        return $this->render('CoreGalleryBundle:default:createCommentsPhoto.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function updateAction($albumSlug, $photoSlug, $commentId, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->findOneBy(array('slug' => $photoSlug));
        $comment = $entityManager->getRepository('CoreGalleryBundle:CommentPhoto')->find($commentId);
        $form = $this->createForm(new CommentPhotoType(), $comment);

        if (!$comment) {
            throw $this->createNotFoundException('Comment Not Found');
        }

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $comment->setUser($user);
                $comment->setPhoto($photo);
                $comment->setUpdatedAt(new \DateTime());
                $entityManager->persist($comment);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('core_photo_show', array('albumSlug' => $albumSlug,'photoSlug' => $photoSlug)));
            }
        }

        return $this->render('CoreGalleryBundle:default:updateCommentPhoto.html.twig', array(
            'form' => $form->createView(),
        ));
    }
//
    public function deleteAction($albumSlug, $photoSlug, $commentId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $comment = $entityManager->getRepository('CoreGalleryBundle:CommentPhoto')->find($commentId);

        if (!$comment) {
            throw $this->createNotFoundException('Comment Not Found');
        }

        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('core_photo_show', array('albumSlug' => $albumSlug, 'photoSlug' => $photoSlug)));
    }
}
