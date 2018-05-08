<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Video;
use AppBundle\Form\VideoType;

/**
 * @Route("/admin/video")
 */
class VideoController extends Controller
{
    /**
     * @Route("/", name="video")
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $videos = $entityManager->getRepository(\AppBundle\Entity\Video::class)->findAll();
        
        return $this->render('admin/video/index.html.twig', ['videos' => $videos]);
    }
    
    /**
     * @Route("/new", name="video_new")
     */
    public function newAction(Request $request)
    {

       $video = new Video();

       $video->setDateCreated(new \DateTime());
       $video->setDateModified(new \DateTime());

       $form = $this->createForm(VideoType::class,$video);
       $form->handleRequest($request);
       
       if($form->isSubmitted() && $form->isValid()){
            $video = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($video);
            $em->flush();
            
            return $this->redirectToRoute('video');
        }

        return $this->render('admin/video/new.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    /**
     * @Route("/edit/{vid_id}", requirements={"vid_id" = "\d+"}, name="video_edit")
     */
    public function editAction($vid_id, Request $request){

        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository(Video::class)->find($vid_id);
        
        if (!$video) {
            throw $this->createNotFoundException(
                'No video found for id '.$vid_id
            );
        }
        
        $form = $this->createForm(VideoType::class,$video);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $video->setDateModified(new \DateTime());
            $em->flush();
            
            return $this->redirectToRoute('video');
            
        }
        
        return $this->render('admin/video/edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
     /**
     * @Route("/delete/{vid_id}", requirements={"vid_id" = "\d+"}, name="video_delete")
     */
    public function deleteAction($vid_id){
        
        $em = $this->getDoctrine()->getManager();
        $video = $em->getRepository(Video::class)->find($vid_id);
        
        $em->remove($video);
        $em->flush();
        
        return $this->redirectToRoute('video');
    }
}
