<?php

namespace PB\PlayableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('PBPlayableBundle:Default:index.html.twig', array('name' => $name));
    }
}
