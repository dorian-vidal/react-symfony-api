<?php

namespace App\Controller;


// use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CookiesController extends AbstractController
{
    public static function setCookies(Request $request, $token, $email)
    {

        
        $response = new Response();
        $cookie = new Cookie('token', $token, time() + (365 * 24 * 60 * 60)); 
        $response->headers->setCookie($cookie);
        $cookie = new Cookie('email', $email, time() + (365 * 24 * 60 * 60)); 
        $response->headers->setCookie($cookie);
        $response->sendHeaders();
    }
}