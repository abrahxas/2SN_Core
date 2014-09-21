<?php

namespace Core\GalleryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\GalleryBundle\Entity\Album;
use Core\GalleryBundle\Form\Type\AlbumType;

class AlbumController extends Controller
{
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $albums = $entityManager->getRepository('CoreGalleryBundle:Album')->findBy(array('user' => $user), array('createdAt' => 'DESC'));

        return $this->render('CoreGalleryBundle:default:index.html.twig', array(
            'albums' => $albums
        ));
    }

    public function showAction($albumSlug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array('slug' => $albumSlug));

        if (!$album) {
            throw $this->createNotFoundException('Album Not Found');
        }

        return $this->render('CoreGalleryBundle:default:show.html.twig', array(
            'album' => $album,
        ));
    }

    public function addAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new AlbumType(), $album = new Album());

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $album->setUser($user);
                $entityManager->persist($album);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('core_gallery_homepage'));
            }
        }

        return $this->render('CoreGalleryBundle:default:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function updateAction(Request $request, $albumSlug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array('slug' => $albumSlug));
        $form = $this->createForm(new AlbumType(), $album);

        if (!$album) {
            throw $this->createNotFoundException('Album Not Found');
        }

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $album->setUser($user);
                $album->setUpdatedAt(new \DateTime());
                $entityManager->persist($album);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('core_gallery_homepage'));
            }
        }

        return $this->render('CoreGalleryBundle:default:update.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function deleteAction($albumSlug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->findOneBy(array('slug' => $albumSlug));

        if (!$album) {
            throw $this->createNotFoundException('Album Not Found');
        }

        $entityManager->remove($album);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('core_gallery_homepage'));
    }
}
