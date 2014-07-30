<?php

namespace Core\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CoreUserBundle:Default:index.html.twig');
    }
}
