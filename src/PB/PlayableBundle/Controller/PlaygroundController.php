<?php

namespace PB\PlayableBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class PlaygroundController extends Controller
{
    public function createAction()
    {
        return new Response('Created');
    }

    public function showAction()
    {
        return new Response('Hello');
    }

}
