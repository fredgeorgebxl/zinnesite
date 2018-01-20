<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Repertoire;
use AppBundle\Form\RepertoireType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
  * @Route("/admin/repertoire")
  */
class RepertoireController extends Controller
{
    /**
     * @Route("/", name="repertoire_list")
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repertoires = $entityManager->getRepository(Repertoire::class)->findBy([], ['title' => 'asc']);

        return $this->render('admin/repertoire/index.html.twig', ['repertoires' => $repertoires]);
    }
    
    /**
     * @Route("/new", name="repertoire_new")
     */
    public function newAction(Request $request)
    {
       $repertoire = new Repertoire();
       $repertoire->setPublished(TRUE);
       $repertoire->setDateCreated(new \DateTime());
       $repertoire->setDateModified(new \DateTime());
       $form = $this->createForm(RepertoireType::class,$repertoire);
       $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $repertoire = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($repertoire);
            $em->flush();
            
            return $this->redirectToRoute('repertoire_list');
        }
        
        return $this->render('admin/repertoire/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/edit/{rep_id}", requirements={"rep_id" = "\d+"}, name="repertoire_edit")
     */
    public function editAction($rep_id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $repertoire = $em->getRepository(Repertoire::class)->find($rep_id);
        
        if (!$repertoire) {
            throw $this->createNotFoundException(
                'No repertoire found for id '.$rep_id
            );
        }
        
        $form = $this->createForm(RepertoireType::class,$repertoire);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $repertoire->setDateModified(new \DateTime());
            $em->flush();
            
            return $this->redirectToRoute('repertoire_list');
        }
        
        return $this->render('admin/repertoire/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

     /**
     * @Route("/delete/{rep_id}", requirements={"rep_id" = "\d+"}, name="repertoire_delete")
     */
    public function deleteAction($rep_id){
        $em = $this->getDoctrine()->getManager();
        $repertoire = $em->getRepository(Repertoire::class)->find($rep_id);
        $em->remove($repertoire);
        $em->flush();
        
        return $this->redirectToRoute('repertoire_list');
    }
}