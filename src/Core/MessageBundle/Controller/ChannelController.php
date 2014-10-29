<?php

namespace Core\MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ChannelController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CoreMessageBundle:Default:index.html.twig', array('name' => $name));
    }
}