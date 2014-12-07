<?php

namespace Core\GalleryBundle\Controller;

use Core\GalleryBundle\Entity\Photo;
use Core\GalleryBundle\Form\Type\PhotoType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;

class PhotosController extends FOSRestController
{
    /**
    * @return array
    * @View()
    */
    public function getPhotosAction(Request $request, $albumId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photos = $entityManager->getRepository('CoreGalleryBundle:Photo')->findBy(array('album' => $albumId), array('createdAt' => 'DESC'));

        return array(
            'photos' => $photos,
        );
    }

    /**
    * @return array
    * @View()
    */
    public function getAdminPhotosAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photos = $entityManager->getRepository('CoreGalleryBundle:Photo')->findAll();

        return array(
            'photos' => $photos,
        );
    }

    /**
    * @return array
    * @View()
    */
    public function getPhotoAction($photoId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($photoId);

        if (!$photo) {
            return array(
                'code' => 404,
                'data' => 'Photo '.$photoId.' Not Found',
            );
        }

        return array(
            'photo' => $photo,
        );
    }

    /**
    * @return array
    * @View()
    */
    public function postPhotosAction(Request $request, $albumId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->find($albumId);
        $form = $this->createForm(new PhotoType(), $photo = new Photo());

        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $photo->setImageFile($request->files->get('imageFile'));
                $photo->setAlbum($album);
                $entityManager->persist($photo);
                $entityManager->flush();

                return array(
                    'code' => 200,
                    'data' => $photo,
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
    public function deletePhotosAction($photoId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($photoId);

        if (!$photo) {
            return array(
                'code' => 404,
                'data' => 'Photo not found',
            );
        }

        $entityManager->remove($photo);
        $entityManager->flush();

        return array(
            'code' => 200,
            'data' => 'Delete done',
        );
    }

    /**
    * @return array
    * @View()
    */
    public function postProfileAction($photoId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $photo = $entityManager->getRepository('CoreGalleryBundle:Photo')->find($photoId);
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!$photo) {
            return array(
                'code' => 404,
                'data' => 'Photo '.$photoId.' Not Found',
            );
        }

        $user->setImageProfile($photo->getImageName());
        $entityManager->persist($user);
        $entityManager->flush();

        return array(
            'code' => 200,
            'data' => $user,
        );
    }
}
