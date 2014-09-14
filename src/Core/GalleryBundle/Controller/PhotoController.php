<?php

namespace Core\GalleryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\GalleryBundle\Entity\Photo;
use Core\GalleryBundle\Form\Type\PhotoType;

class PhotoController extends Controller
{
    public function indexAction($albumId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photos = $entityManager->getRepository('CoreGalleryBundle:Photo')->findBy(array('album' => $albumId), array('createdAt' => 'DESC'));

        return $this->render('CoreGalleryBundle:default:index-photo.html.twig', array(
            'albumId' => $albumId,
            'photos' => $photos
        ));
    }

    public function showAction($albumId, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($id);

        return $this->render('CoreGalleryBundle:default:show-photo.html.twig', array(
            'albumId' => $albumId,
            'photo' => $photo
        ));
    }

    public function updateAction($albumId, $id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->find($albumId);
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($id);
        $form = $this->createForm(new PhotoType(), $photo);

        if ($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isValid()){
                $photo->setAlbum($album);
                $photo->setUpdatedAt(new \DateTime());
                $entityManager->persist($photo);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('core_album_show', array('id' => $albumId)));
            }
        }

        return $this->render('CoreGalleryBundle:default:update-photo.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    public function deleteAction($albumId, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($id);
        if ($photo)
        {
            $entityManager->remove($photo);
            $entityManager->flush();
        }
        return $this->redirect($this->generateUrl('core_album_show', array('id' => $albumId)));
    }
}
