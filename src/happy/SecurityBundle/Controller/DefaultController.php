<?php

namespace happy\SecurityBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends BaseController
{
    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return Response
     */
    function renderLogin(array $data)
    {
        return $this->render('happySecurityBundle:Security:login.html.twig', $data);
    }
}



