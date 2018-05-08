<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Parameter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ParameterListType;
use AppBundle\Form\NewParameterType;

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
    /**
     * @Route("/add", name="parameter_add")
     */
    public function addAction(Request $request)
    {
       $parameter = new Parameter();
       $form = $this->createForm(NewParameterType::class, $parameter);
       $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $parameter = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($parameter);
            $em->flush();
            
            return $this->redirectToRoute('parameter_list');
        }
        
        return $this->render('admin/parameters/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/delete/{ent_id}", requirements={"ent_id" = "\d+"}, name="parameter_delete")
     */
    public function deleteAction($ent_id)
    {
        $em = $this->getDoctrine()->getManager();
        $repertoire = $em->getRepository(Parameter::class)->find($ent_id);
        $em->remove($repertoire);
        $em->flush();
        
        return $this->redirectToRoute('parameter_list');
    }
}
