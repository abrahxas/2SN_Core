<?php

namespace Core\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GalleryController extends Controller
{
    public function indexAction()
    {
        return $this->render('CoreGalleryBundle:default:index.html.twig');
    }
}
