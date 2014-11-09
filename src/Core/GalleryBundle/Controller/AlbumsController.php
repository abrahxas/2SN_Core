<?php

namespace Core\GalleryBundle\Controller;

use Core\GalleryBundle\Entity\Album;
use Core\GalleryBundle\Form\Type\AlbumType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AlbumsController extends FOSRestController
{
    /**
    * @return array
    * @View()
    */
    public function getAlbumsAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $albums = $entityManager->getRepository('CoreGalleryBundle:Album')->findBy(array('user' => $user), array('createdAt' => 'DESC'));

        return array('albums', $albums);
    }

    /**
    * @return array
    * @View()
    */
    public function getAlbumAction($albumSlug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array('slug' => $albumSlug));

        if (!$album) {
            throw $this->createNotFoundException('Album Not Found');
        }

        return array('album' => $album);
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

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $album->setUser($user);
                $entityManager->persist($album);
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
    public function putAlbumsAction(Request $request, $albumSlug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array('slug' => $albumSlug));
        $form = $this->createForm(new AlbumType(), $album);

        if (!$album) {
            throw $this->createNotFoundException('Album Not Found');
        }
        $jsonPost = json_decode($request->getContent(), true);
        if ($request->isMethod('PUT')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $album->setUser($user);
                $album->setUpdatedAt(new \DateTime());
                $entityManager->persist($album);
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
    public function deleteAlbumsAction($albumSlug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array('slug' => $albumSlug));

        if (!$album) {
            throw $this->createNotFoundException('Album Not Found');
        }

        $entityManager->remove($album);
        $entityManager->flush();

        return array('code' => 200, 'text' => 'DELETE OK');
    }
}
