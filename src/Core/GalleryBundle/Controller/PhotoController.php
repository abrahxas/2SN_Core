<?php

namespace Core\GalleryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\GalleryBundle\Entity\Photo;
use Core\GalleryBundle\Form\Type\PhotoType;

class PhotoController extends Controller
{
    public function indexAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array('slug' => $request->get('albumSlug')));
        $photos = $entityManager->getRepository('CoreGalleryBundle:Photo')->findBy(array('album' => $album->getId()), array('createdAt' => 'DESC'));

        return $this->render('CoreGalleryBundle:default:index-photo.html.twig', array(
            'photos' => $photos
        ));
    }

    public function indexAllAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photos = $entityManager->getRepository('CoreGalleryBundle:Photo')->findAll();

        return $this->render('CoreGalleryBundle:default:index-photoAll.html.twig', array(
            'photos' => $photos
        ));
    }

    public function showAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($request->get('photoId'));

        if (!$photo) {
            throw $this->createNotFoundException('Photo ' . $request->get('photoId') . ' Not Found');
        }

        return $this->render('CoreGalleryBundle:default:show-photo.html.twig', array(
            'photo' => $photo
        ));
    }

    public function addAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array('slug' => $request->get('albumSlug')));
        $form = $this->createForm(new PhotoType(), $photo = new Photo());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $photo->setAlbum($album);
                $entityManager->persist($photo);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('core_album_show', array('albumSlug' => $request->get('albumSlug'))));
            }
        }

        return $this->render('CoreGalleryBundle:default:create-photo.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function updateAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array('slug' => $request->get('albumSlug')));
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($request->get('photoId'));

        if (!$photo) {
            throw $this->createNotFoundException('Photo ' . $request->get('photoId') . ' Not Found');
        }

        $form = $this->createForm(new PhotoType(), $photo);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $photo->setAlbum($album);
                $photo->setUpdatedAt(new \DateTime());
                $entityManager->persist($photo);
                $entityManager->flush();

                if ($request->get('albumSlug') != null)
                    return $this->redirect($this->generateUrl('core_album_show', array('albumSlug' => $request->get('albumSlug'))));
                else
                    return $this->redirect($this->generateUrl('core_photo_all'));
            }
        }

        return $this->render('CoreGalleryBundle:default:update-photo.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    public function deleteAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($request->get('photoId'));

        if (!$photo) {
            throw $this->createNotFoundException('Photo ' . $request->get('photoId') . ' Not Found');
        }

        $entityManager->remove($photo);
        $entityManager->flush();

        if ($request->get('albumSlug') != null)
            return $this->redirect($this->generateUrl('core_album_show', array('albumSlug' => $request->get('albumSlug'))));
        else
            return $this->redirect($this->generateUrl('core_photo_all'));
    }
}
