<?php

namespace Iepg\Bundle\Cas\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/cas_anonymous-home", name="cas_anonymous_home")
     */
    public function index(): Response
    {
        return $this->render('@cas_connection/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/cas_user-home", name="cas_user_home")
     */
    public function indexUser(): Response
    {
        $user = $this->getUser();

        return $this->render('@cas_connection/index_user.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user,
        ]);
    }

    /**
     * @Route("/cas_admin-home", name="cas_admin_home")
     */
    public function indexAdmin(): Response
    {
        $admin = $this->getUser();

        return $this->render('@cas_connection/index_admin.html.twig', [
            'controller_name' => 'HomeController',
            'admin' => $admin,
        ]);
    }

    /**
     * @Route("/cas_unknow-home", name="cas_user_unknow")
     */
    public function indexUserUnknow(): Response
    {
        return $this->render('@cas_connection/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/cas_login", name="cas_login")
     */
    public function cas_authenticator(): Response
    {
        return $this->redirectToRoute('cas_dispatcher');
    }

    /**
    * @Route("/cas_dispatcher", name="cas_dispatcher", methods={"GET"})
    */
    public function dispatcher()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('cas_admin_home');
        };
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('cas_user_home');
        };
        return $this->redirectToRoute('cas_anonymous_home');
    }
}
