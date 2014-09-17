<?php

namespace Core\GalleryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Core\GalleryBundle\Entity\Album;
use Core\GalleryBundle\Form\Type\AlbumType;
use Core\GalleryBundle\Entity\Photo;
use Core\GalleryBundle\Form\Type\PhotoType;

class GalleryController extends Controller
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

    public function showAlbumAction(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->find($id);
        $form = $this->createForm(new PhotoType(), $photo = new Photo());

        if ($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isValid()){
                $photo->setAlbum($album);
                $entityManager->persist($photo);
                $entityManager->flush();
                return $this->redirect($this->generateUrl('core_album_show', array('id' => $id)));
            }
        }

        return $this->render('CoreGalleryBundle:default:show.html.twig', array(
            'form' => $form->createView(),
            'album' => $album,
        ));
    }

    public function addAlbumAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new AlbumType(), $album = new Album());

        if ($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isValid()){
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

    public function updateAlbumAction(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();

        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->find($id);
        $form = $this->createForm(new AlbumType(), $album);

        if ($request->isMethod('POST')){
            $form->handleRequest($request);
            if ($form->isValid()){
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

    public function deleteAlbumAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $album = $entityManager->getRepository('CoreGalleryBundle:Album')->find($id);

        if ($album){
            $entityManager->remove($album);
            $entityManager->flush();
        }

        return $this->redirect($this->generateUrl('core_gallery_homepage'));
    }
}
