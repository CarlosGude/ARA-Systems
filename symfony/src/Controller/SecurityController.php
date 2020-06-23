<?php

namespace App\Controller;

use App\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="app_login")
     * @Route("/company/{company}", name="app_login_company")
     */
    public function login(AuthenticationUtils $authenticationUtils, string $company = null): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('front_dashboard');
        }

        if ($company) {
            $company = $this->getDoctrine()->getRepository(Company::class)->findOneBy(['slug' => $company]);
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'company' => $company,
            'error' => $error,
        ]);
    }
}
