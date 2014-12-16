<?php

namespace Core\GalleryBundle\Controller;

use Core\GalleryBundle\Entity\Album;
use Core\GalleryBundle\Form\Type\AlbumType;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;

class AlbumsController extends FOSRestController
{
    /**
    * @return array
    * @View()
    */
    public function getAlbumsAction($userId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository('CoreUserBundle:User')->find($userId);
        $albums = $entityManager->getRepository('CoreGalleryBundle:Album')->findBy(array('user' => $user), array('createdAt' => 'DESC'));

        return array(
            'albums' => $albums,
        );
    }

    /**
    * @return array
    * @View()
    * @Get("/album/{albumId}")
    */
    public function getAlbumAction($albumId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->find($albumId);

        if (!$album) {
            return array(
                'code' => 404,
                'data' => 'Album not found',
            );
        }

        return array(
            'album' => $album,
        );
    }

    /**
    * @return array
    * @View()
    */
    public function postAlbumsAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new AlbumType(), $album = new Album());
        $jsonPost = json_decode($request->getContent(), true);

        if ($request->isMethod('POST') && !empty($jsonPost)) {
            $form->bind($jsonPost);
            if ($form->isSubmitted() && $form->isValid()) {
                $album->setUser($user);
                $entityManager->persist($album);
                $entityManager->flush();

                return array(
                    'code' => 200,
                    'data' => $album,
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
    public function putAlbumsAction(Request $request, $albumId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->find($albumId);
        $form = $this->createForm(new AlbumType(), $album);

        if (!$album) {
            return array(
                'code' => 404,
                'data' => 'Album not found',
            );
        }
        $jsonPost = json_decode($request->getContent(), true);
        if ($request->isMethod('PUT') && !empty($jsonPost)) {
            $form->bind($jsonPost);
            if ($form->isSubmitted() && $form->isValid()) {
                $album->setUser($user);
                $album->setUpdatedAt(new \DateTime());
                $entityManager->persist($album);
                $entityManager->flush();

                return array(
                    'code' => 200,
                    'data' => $album,
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
    public function deleteAlbumsAction($albumId)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->find($albumId);

        if (!$album) {
            return array(
                'code' => 404,
                'data' => 'Album not found',
            );
        }

        $entityManager->remove($album);
        $entityManager->flush();

        return array(
            'code' => 200,
            'text' => 'Delete done',
        );
    }
}
