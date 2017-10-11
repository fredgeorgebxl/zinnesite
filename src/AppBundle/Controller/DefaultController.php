<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        // get slideshow images
        $entityManager = $this->getDoctrine()->getManager();
        $slideshow = $entityManager->getRepository(\AppBundle\Entity\Gallery::class)->findOneBy(['homeslide' => 1]);
        
        return $this->render('default/index.html.twig', ['ishome' => true, 'slideshow' => $slideshow]);
    }
    
    /**
     * @Route("/agenda", name="agenda")
     */
    public function agendaAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(\AppBundle\Entity\Event::class);
        $queryEvents = $repository->createQueryBuilder('ev')
                ->where('ev.published = 1')
                ->where('ev.date >= :datenow')
                ->setParameter('datenow', date('Y-m-d H:i:s'))
                ->orderBy('ev.date', 'DESC')
                ->getQuery();
        $pastEventsQuery = $repository->createQueryBuilder('ev')
                ->where('ev.published = 1')
                ->where('ev.date < :datenow')
                ->setParameter('datenow', date('Y-m-d H:i:s'))
                ->orderBy('ev.season', 'DESC')
                ->orderBy('ev.date', 'DESC')
                ->getQuery();
        $events = $queryEvents->getResult();
        $pastevents = $pastEventsQuery->getResult();
        
        return $this->render('default/agenda.html.twig', ['events' => $events, 'pastevents' => $pastevents]);
    }
    
    /**
     * @Route("/agenda/{slug}", name="event")
     */
    public function eventAction($slug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $event = $entityManager->getRepository(\AppBundle\Entity\Event::class)->findOneBy([ 'slug' => $slug, 'published' => 1]);
        
        if (!$event) {
            throw $this->createNotFoundException(
                'No event found for slug '.$slug
            );
        }
        
        return $this->render('default/event.html.twig', ['event' => $event]);
    }

    /**
     * @Route("/repertoire", name="repertoire")
     */
    public function repertoireAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repertoire = $entityManager->getRepository(\AppBundle\Entity\Repertoire::class)->findBy(['published' => 1, 'active' => 1], ['title' => 'asc']);
        $oldrepertoire = $entityManager->getRepository(\AppBundle\Entity\Repertoire::class)->findBy(['published' => 1, 'active' => 0], ['title' => 'asc']);
        
        return $this->render('default/repertoire.html.twig', ['repertoire' => $repertoire, 'oldrepertoire' => $oldrepertoire]);
    }
    
    /**
     * @Route("/membres", name="membres")
     */
    public function membresAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(\AppBundle\Entity\User::class);
        $queryMembers = $repository->createQueryBuilder('m')
                ->where('m.enabled = 1')
                ->where('m.voice != \'chef\'')
                ->orderBy('m.firstname', 'ASC')
                ->getQuery();
        $members = $queryMembers->getResult();
        $chef = $entityManager->getRepository(\AppBundle\Entity\User::class)->findOneBy([ 'voice' => 'chef', 'enabled' => 1]);
        
        return $this->render('default/membres.html.twig', ['chef' => $chef, 'members' => $members]);
    }
    
    /**
     * @Route("/photos", name="photos")
     */
    public function photosAction()
    {        
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(\AppBundle\Entity\Gallery::class);
        $qb = $repository->createQueryBuilder('gal');
        $queryEvents = $qb
                ->where('gal.homeslide != 1')
                ->orWhere($qb->expr()->isNull('gal.homeslide'))
                ->orderBy('gal.datecreated', 'DESC')
                ->getQuery();
        $galleries = $queryEvents->getResult();
        
        return $this->render('default/photos.html.twig', ['galleries' => $galleries]);
    }
    
    /**
     * @Route("/photos/{slug}", name="gallery")
     */
    public function galleryAction($slug)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $gallery = $entityManager->getRepository(\AppBundle\Entity\Gallery::class)->findOneBy([ 'slug' => $slug, 'published' => 1]);
        
        return $this->render('default/gallery.html.twig', ['gallery' => $gallery]);
    }
    
    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction(Request $request)
    {
        $form = $this->createForm(\AppBundle\Form\ContactType::class);
        $form->handleRequest($request);
        $messagesent = NULL;
        
        if($form->isSubmitted() &&  $form->isValid()){
            $name = $form['name']->getData();
            $email = $form['email']->getData();
            $subject = $form['subject']->getData();
            $message = $form['message']->getData();
            
            $swiftmessage = (new \Swift_Message($subject))
               ->setFrom($email)
               ->setTo($this->getParameter('contact_mail'))
               ->setBody($this->renderView('mails/contactmail.html.twig',array('name' => $name, 'email' => $email, 'message' => $message)),'text/html');
            
            if($this->get('mailer')->send($swiftmessage)){
                $messagesent = TRUE;
            } else {
                $messagesent = FALSE;
            }
        }
        
        return $this->render('default/contact.html.twig', ['form' => $form->createView(), 'messagesent' => $messagesent]);
    }
}
