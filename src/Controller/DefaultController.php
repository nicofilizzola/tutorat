<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig', [
        ]);
    }

    /**
     * @Route("/become-tutor", name="app_become-tutor", methods={"GET", "POST"})
     */
    public function becomeTutor(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser() || in_array("ROLE_TUTOR", $this->getUser()->getRoles())){
            return $this->redirectToRoute('app_home');
        }

        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('become-tutor', $submittedToken)) {
            $this->getUser()->setRoles(["ROLE_STUDENT", "ROLE_TUTOR"]);
            $this->getUser()->setIsValid(1);

            $em->persist($this->getUser());
            $em->flush();

            // send mail to admin

            $this->addFlash('Success', 'Votre demande a bien été envoyée ! Vous aurez une réponse dans environ une semaine.');
            return $this->redirectToRoute('app_home');
        }


        return $this->render('default/become-tutor.html.twig', [
            
        ]);
    }
}
