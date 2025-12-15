<?php

namespace Iepg\Bundle\Cas\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CasController extends AbstractController
{
    #[Route('/cas_login', name: 'cas_login')]
    public function cas_authenticator(): Response
    {
        return $this->redirectToRoute('cas_dispatcher');
    }

    public function indexUserUnknow(): Response
    {
                return $this->render('@cas_connection/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/cas_dispatcher', name: 'cas_dispatcher', methods: 'GET')]
    public function dispatcher()
    {
        if ($this->isGranted('ROLE_ADMIN')) {

            $admin = $this->getUser();

            return $this->render('@cas_connection/index_admin.html.twig', [
                'controller_name' => 'HomeController',
                'admin' => $admin,
            ]);
        };

        if ($this->isGranted('ROLE_USER')) {

            $user = $this->getUser();

            return $this->render('@cas_connection/index_user.html.twig', [
                'controller_name' => 'HomeController',
                'user' => $user,
            ]);
        };

        return $this->render('@cas_connection/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
