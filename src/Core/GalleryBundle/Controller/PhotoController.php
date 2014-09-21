<?php

namespace Core\GalleryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\GalleryBundle\Entity\Photo;
use Core\GalleryBundle\Form\Type\PhotoType;

class PhotoController extends Controller
{
    public function indexAction($albumSlug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array(
            'slug' => $albumSlug,
        ));
        $photos = $entityManager->getRepository('CoreGalleryBundle:Photo')->findBy(array('album' => $album->getId()), array('createdAt' => 'DESC'));

        return $this->render('CoreGalleryBundle:default:index-photo.html.twig', array(
            'albumSlug' => $albumSlug,
            'photos' => $photos
        ));
    }

    public function showAction($albumSlug, $photoSlug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->findOneBy(array(
            'slug' => $photoSlug,
        ));

        return $this->render('CoreGalleryBundle:default:show-photo.html.twig', array(
            'albumSlug' => $albumSlug,
            'photo' => $photo
        ));
    }

    public function addAction($albumSlug, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array(
            'slug' => $albumSlug,
        ));
        $form = $this->createForm(new PhotoType(), $photo = new Photo());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $photo->setAlbum($album);
                $entityManager->persist($photo);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('core_album_show', array('albumSlug' => $albumSlug)));
            }
        }

        return $this->render('CoreGalleryBundle:default:create-photo.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function updateAction($albumSlug, $photoSlug, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array(
            'slug' => $albumSlug,
        ));
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->findOneBy(array(
            'slug' => $photoSlug,
        ));
        $form = $this->createForm(new PhotoType(), $photo);

        if ($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isValid()){
                $photo->setAlbum($album);
                $photo->setUpdatedAt(new \DateTime());
                $entityManager->persist($photo);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('core_album_show', array('albumSlug' => $albumSlug)));
            }
        }

        return $this->render('CoreGalleryBundle:default:update-photo.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    public function deleteAction($albumSlug, $photoSlug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->findOneBy(array(
            'slug' => $photoSlug,
        ));;
        if ($photo)
        {
            $entityManager->remove($photo);
            $entityManager->flush();
        }
        return $this->redirect($this->generateUrl('core_album_show', array('albumSlug' => $albumSlug)));
    }
}
