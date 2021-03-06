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
        // get videos for homepage
        $videos = $entityManager->getRepository(\AppBundle\Entity\Video::class)->findBy([],[],10);
        if (count($videos)){
            $index = rand(0, (count($videos)-1));
            $video = $videos[$index];
        }else{
            $video = null;
        }
        
        // get random pictures from galleries
        $photos_rep = $entityManager->getRepository(\AppBundle\Entity\ResponsiveImage::class);
        $qb = $photos_rep->createQueryBuilder('p');
        $qb->select('p.id')
                ->innerJoin('p.gallery', 'g', 'WITH', $qb->expr()->orx(
                        $qb->expr()->neq('g.homeslide',1),
                        $qb->expr()->isNull('g.homeslide')
                ));
        $pictures_id = $qb->getQuery()->getArrayResult();
        $selected = [];
        $query_array = [];
        if (count($pictures_id)){
            $selected = array_rand($pictures_id, 5);
        }
        foreach ($selected as $cid){
            $query_array[] = $pictures_id[$cid]["id"];
        }
        $pictures = $photos_rep->createQueryBuilder('p')
                ->where('p.id IN (:ids)')
                ->setParameter('ids', $query_array)
                ->getQuery()
                ->getResult();
        
        return $this->render('default/index.html.twig', ['ishome' => true, 'slideshow' => $slideshow, 'video' => $video, 'pictures' => $pictures]);
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
                ->andWhere('ev.date >= :datenow')
                ->setParameter('datenow', date('Y-m-d H:i:s'))
                ->orderBy('ev.date', 'ASC')
                ->getQuery();
        $pastEventsQuery = $repository->createQueryBuilder('ev')
                ->where('ev.published = 1')
                ->andWhere('ev.date < :datenow')
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
        $gal_id = $event->getGallery();
        if ($gal_id != 0){
            $gallery = $entityManager->getRepository(\AppBundle\Entity\Gallery::class)->findOneBy(['id' => $gal_id]);   
        } else {
            $gallery = NULL;
        }
        return $this->render('default/event.html.twig', ['event' => $event, 'gallery' => $gallery]);
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
                ->andWhere('m.voice != \'chef\'')
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
                ->andWhere('gal.published = 1')
                ->orWhere($qb->expr()->isNull('gal.homeslide'))
                ->orderBy('gal.date', 'DESC')
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
        $gallery_rep = $entityManager->getRepository(\AppBundle\Entity\Gallery::class);
        $gallery = $gallery_rep->createQueryBuilder('g')
                ->leftJoin('g.pictures', 'pic')
                ->where('g.slug = :slug')
                ->andWhere('g.published = 1')
                ->setParameter('slug', $slug)
                ->addSelect('pic')
                ->getQuery()
                ->getSingleResult();
        
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
    
    /**
     * @Route("/nous-rejoindre", name="joinus")
     */
    public function joinusAction(Request $request)
    {
        return $this->render('default/joinus.html.twig');
    }
    
    /**
     * @Route("/sitemap.{_format}", name="sitemap", Requirements={"_format" = "xml"})
     */
    public function sitemapAction(Request $request)
    {
        return $this->render('sitemap.xml.twig', ['urls' => $this->get('app.sitemap')->generateUrls()]);
    }
}
