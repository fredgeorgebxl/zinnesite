<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Event;
use AppBundle\Form\EventType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
  * @Route("/admin/event")
  */
class EventController extends Controller{
    /**
     * @Route("/", name="event_list")
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $events = $entityManager->getRepository(Event::class)->findBy([], ['datecreated' => 'desc']);

        return $this->render('admin/event/index.html.twig', ['events' => $events]);
    }
    
    /**
     * @Route("/new", name="event_new")
     */
    public function newAction(Request $request)
    {
       $event = new Event();
       $event->setPublished(TRUE);
       $event->setDateCreated(new \DateTime());
       $event->setDateModified(new \DateTime());
       $form = $this->createForm(EventType::class,$event);
       $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $event = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();
            
            return $this->redirectToRoute('event_list');
        }
        
        return $this->render('admin/event/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/edit/{ev_id}", requirements={"ev_id" = "\d+"}, name="event_edit")
     */
    public function editAction($ev_id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($ev_id);
        
        if (!$event) {
            throw $this->createNotFoundException(
                'No event found for id '.$ev_id
            );
        }
        
        $form = $this->createForm(EventType::class,$event);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $event->setDateModified(new \DateTime());
            $em->flush();
            
            return $this->redirectToRoute('event_list');
        }
        
        return $this->render('admin/event/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/delete/{ev_id}", requirements={"ev_id" = "\d+"}, name="event_delete")
     */
    public function deleteAction($ev_id){
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Repertoire::class)->find($ev_id);
        $em->remove($event);
        $em->flush();
        
        return $this->redirectToRoute('event_list');
    }
}
