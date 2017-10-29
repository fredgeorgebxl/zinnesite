<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/admin")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="admin_home")
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $homepage_slideshow = $entityManager->getRepository(\AppBundle\Entity\Gallery::class)->findOneBy(['homeslide' => 1]);
        
        return $this->render('admin/default/index.html.twig', ['slideshow' => $homepage_slideshow]);
    }
}
