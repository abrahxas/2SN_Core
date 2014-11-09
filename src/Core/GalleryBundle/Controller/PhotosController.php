<?php

namespace Core\GalleryBundle\Controller;

use Core\GalleryBundle\Entity\Photo;
use Core\GalleryBundle\Form\Type\PhotoType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PhotosController extends FOSRestController
{
    /**
    * @return array
    * @View()
    */
    public function getPhotosAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array('slug' => $request->get('albumSlug')));
        $photos = $entityManager->getRepository('CoreGalleryBundle:Photo')->findBy(array('album' => $album->getId()), array('createdAt' => 'DESC'));

        return array('photos' => $photos);
    }

    /**
    * @return array
    * @View()
    */
    public function getAdminPhotosAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photos = $entityManager->getRepository('CoreGalleryBundle:Photo')->findAll();

        return array('photos' => $photos);
    }

    /**
    * @return array
    * @View()
    */
    public function getPhotoAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($request->get('photoId'));

        if (!$photo) {
            throw $this->createNotFoundException('Photo ' . $request->get('photoId') . ' Not Found');
        }

        return array('photo' => $photo);
    }

    /**
    * @return array
    * @View()
    */
    public function postPhotosAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array('slug' => $request->get('albumSlug')));
        $form = $this->createForm(new PhotoType(), $photo = new Photo());
        $jsonPost = json_decode($request->getContent(), true);

        if ($request->isMethod('POST')) {
            $form->bind($jsonPost);
            if ($form->isValid()) {
                $photo->setAlbum($album);
                $entityManager->persist($photo);
                $entityManager->flush();
                return array('code' => 200, 'text' => 'POST OK');
            }
        }

        return array('code' => 400, $form);
    }

    /**
    * @return array
    * @View()
    */
    public function putPhotosAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array('slug' => $request->get('albumSlug')));
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($request->get('photoId'));

        if (!$photo) {
            throw $this->createNotFoundException('Photo ' . $request->get('photoId') . ' Not Found');
        }

        $form = $this->createForm(new PhotoType(), $photo);
        $jsonPost = json_decode($request->getContent(), true);
        if ($request->isMethod('POST')) {
            $form->bind($jsonPost);
            if ($form->isValid()) {
                $photo->setAlbum($album);
                $photo->setUpdatedAt(new \DateTime());
                $entityManager->persist($photo);
                $entityManager->flush();

                return array('code' => 200, 'text' => 'PUT OK');
            }
        }

        return array('code' => 400, $form);
    }


    /**
    * @return array
    * @View()
    */
    public function deletePhotosAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($request->get('photoId'));

        if (!$photo) {
            throw $this->createNotFoundException('Photo ' . $request->get('photoId') . ' Not Found');
        }

        $entityManager->remove($photo);
        $entityManager->flush();

        return array('code' => 200, 'text' => 'DELETE OK');
    }

    public function postPhotoProfileAction($albumSlug, $photoId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($photoId);
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!$photo) {
            throw $this->createNotFoundException('Photo ' . $request->get('photoId') . ' Not Found');
        }

        $user->setImageProfile($photo->getImageName());
        $entityManager->persist($user);
        $entityManager->flush();

        return array('code' => 200, 'text' => 'POST OK');
    }
}
