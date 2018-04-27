<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Parameter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ParameterListType;

/**
  * @Route("/admin/parameter")
  */
class ParameterController extends Controller{
    /**
     * @Route("/", name="parameter_list")
     */
    public function indexAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $parameters = $entityManager->getRepository(Parameter::class)->findAll();
        
        $default_values = array('parameters' => $parameters);
        $form = $this->createForm(ParameterListType::class,$default_values);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            return $this->redirectToRoute('admin_home');
        }

        return $this->render('admin/parameters/index.html.twig', ['form' => $form->createView()]);
    }
}
