<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', ['ishome' => true]);
    }
    
    /**
     * @Route("/agenda", name="agenda")
     */
    public function agendaAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(\AppBundle\Entity\Event::class);
        $queryEvents = $repository->createQueryBuilder('ev')
                ->where('ev.date >= :datenow')
                ->setParameter('datenow', date('Y-m-d H:i:s'))
                ->orderBy('ev.date', 'DESC')
                ->getQuery();
        $pastEventsQuery = $repository->createQueryBuilder('ev')
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
        $event = $entityManager->getRepository(\AppBundle\Entity\Event::class)->findOneBySlug($slug);
        
        return $this->render('default/event.html.twig', ['event' => $event]);
    }

    /**
     * @Route("/repertoire", name="repertoire")
     */
    public function repertoireAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repertoire = $entityManager->getRepository(\AppBundle\Entity\Repertoire::class)->findBy([], ['title' => 'asc']);
        
        return $this->render('default/repertoire.html.twig', ['repertoire' => $repertoire]);
    }
    
    /**
     * @Route("/membres", name="membres")
     */
    public function membresAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $membres = $entityManager->getRepository(\AppBundle\Entity\User::class)->findBy([], ['firstname' => 'asc']);
        
        return $this->render('default/membres.html.twig', ['membres' => $membres]);
    }
    
    /**
     * @Route("/photos", name="photos")
     */
    public function photosAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $galleries = $entityManager->getRepository(\AppBundle\Entity\Gallery::class)->findBy([], ['datecreated' => 'desc']);
        
        return $this->render('default/photos.html.twig', ['galleries' => $galleries]);
    }
    
    /**
     * @Route("/contact", name="contact")
     */
    public function contactAction()
    {
        
        return $this->render('default/contact.html.twig');
    }
    
}
