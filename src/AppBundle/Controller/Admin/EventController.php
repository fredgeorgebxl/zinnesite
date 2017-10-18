<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Event;
use AppBundle\Form\EventType;
use AppBundle\Entity\ResponsiveImage;
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
        $em = $this->getDoctrine()->getManager();
        $event = new Event();
        $event->setPublished(TRUE);
        $event->setDateCreated(new \DateTime());
        $event->setDateModified(new \DateTime());

        $zinneparams = $this->getParameter('zinne');
        $form = $this->createForm(EventType::class,$event,['seasons-available' => $zinneparams['seasons-available'], 'entity_manager' => $em]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $event = $form->getData();
            
            $file = $form->get('picture')->get('file')->getData();
            $alt = $form->get('picture')->get('alt')->getData();
            $title = $form->get('picture')->get('title')->getData();
            if($file){
                $picture = $this->initPicture($file, $alt, $title);
                $event->setPicture($picture);
            } else {
                $event->setPicture(NULL);
            }

            $em->persist($event);
            $em->flush();
            
            if($form->get('picture')->has('add_image') && $form->get('picture')->get('add_image')->isClicked()){
                return $this->redirectToRoute('event_edit', ['ev_id' => $event->getId()]);
            } else {
                return $this->redirectToRoute('event_list');
            }
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
        
        $zinneparams = $this->getParameter('zinne');
        $form = $this->createForm(EventType::class,$event,['seasons-available' => $zinneparams['seasons-available'], 'entity_manager' => $em]);
        $form->handleRequest($request);
        $picture = $event->getPicture();
        
        if($form->isSubmitted() && $form->isValid()){
            $event->setDateModified(new \DateTime());
            
            // Get data from the 'file' field
            $file = $form->get('picture')->get('file')->getData();
            
            if($file){
                // if there's no image, create a new one
                if(is_null($picture)){
                    $alt = $form->get('picture')->get('alt')->getData();
                    $title = $form->get('picture')->get('title')->getData();
                    $picture = $this->initPicture($file, $alt, $title);
                }
                // If there's an image, delete the current file...
                if(!empty($picture->getPath())){
                    $this->get('responsive_image')->deleteImageFiles($picture);
                }

                // ...and upload the new file
                $picture->setFile($file);
                $this->get('responsive_image.uploader')->upload($picture);
            }
            
            // Remove image if necessary
            if($form->get('picture')->has('remove_image') && $form->get('picture')->get('remove_image')->isClicked()){
                $this->get('responsive_image')->deleteImageFiles($picture);
                $event->setPicture(NULL);
            }
            
            // Reset picture to NULL if the picture has no path to avoid an SQL error
            if(!is_null($picture) && empty($picture->getPath())){
                $event->setPicture(NULL);
            }
            
            $em->flush();
            
            if(($form->get('picture')->has('remove_image') && $form->get('picture')->get('remove_image')->isClicked()) || ($form->get('picture')->has('add_image') && $form->get('picture')->get('add_image')->isClicked())){
                return $this->redirectToRoute('event_edit', ['ev_id' => $ev_id]);
            } else {
                return $this->redirectToRoute('event_list');
            }
        }
        
        return $this->render('admin/event/edit.html.twig', array(
            'form' => $form->createView(),
            'picture' => $picture,
        ));
    }
    
    /**
     * @Route("/delete/{ev_id}", requirements={"ev_id" = "\d+"}, name="event_delete")
     */
    public function deleteAction($ev_id){
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Event::class)->find($ev_id);
        
         // Delete picture
        $picture = $event->getPicture();
        if (!is_null($picture)){
            $this->get('responsive_image')->deleteImageFiles($picture);
        }
        
        $em->remove($event);
        $em->flush();
        
        return $this->redirectToRoute('event_list');
    }
    
    public function initPicture($file, $alt, $title){
        $picture = new ResponsiveImage();
        if ($file) {
            $picture->setFile($file);
            $this->get('responsive_image.uploader')->upload($picture);
        } else {
            $picture->setPath('');
        }
        $picture->setAlt($alt);
        $picture->setTitle($title);

        return $picture;
    }
}
