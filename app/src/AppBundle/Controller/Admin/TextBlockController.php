<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\TextBlock;
use AppBundle\Form\TextBlockType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
  * @Route("/admin/textblock")
  */
class TextBlockController extends Controller
{
    /**
     * @Route("/", name="textblock_list")
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $textblocks = $entityManager->getRepository(TextBlock::class)->findBy([], ['name' => 'asc']);

        return $this->render('admin/textblock/index.html.twig', ['textblocks' => $textblocks]);
    }
    
    /**
     * @Route("/new", name="textblock_new")
     */
    public function newAction(Request $request)
    {
       $textblock = new TextBlock();
       $textblock->setPublished(TRUE);
       $textblock->setDateCreated(new \DateTime());
       $textblock->setDateModified(new \DateTime());
       $form = $this->createForm(TextBlockType::class,$textblock);
       $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $textblock = $form->getData();
            $file = $form->get('picture')->get('file')->getData();
            $alt = $form->get('picture')->get('alt')->getData();
            $title = $form->get('picture')->get('title')->getData();
            if($file){
                $picture = $this->initPicture($file, $alt, $title);
                $textblock->setPicture($picture);
            } else {
                $textblock->setPicture(NULL);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($textblock);
            $em->flush();
            
            return $this->redirectToRoute('textblock_list');
        }
        
        return $this->render('admin/textblock/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/edit/{ent_id}", requirements={"ent_id" = "\d+"}, name="textblock_edit")
     */
    public function editAction($ent_id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $textblock = $em->getRepository(TextBlock::class)->find($ent_id);
        
        if (!$textblock) {
            throw $this->createNotFoundException(
                'No textblock found for id '.$ent_id
            );
        }
        
        $form = $this->createForm(TextBlockType::class,$textblock);
        $form->handleRequest($request);
        $picture = $textblock->getPicture();
        
        if($form->isSubmitted() && $form->isValid()){
            $textblock->setDateModified(new \DateTime());
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
                $textblock->setPicture(NULL);
            }
            
            // Reset picture to NULL if the picture has no path to avoid an SQL error
            if(!is_null($picture) && empty($picture->getPath())){
                $textblock->setPicture(NULL);
            }
            $em->flush();
            
            if(($form->get('picture')->has('remove_image') && $form->get('picture')->get('remove_image')->isClicked()) || ($form->get('picture')->has('add_image') && $form->get('picture')->get('add_image')->isClicked())){
                return $this->redirectToRoute('textblock_edit', ['ent_id' => $ent_id]);
            } else {
                return $this->redirectToRoute('textblock_list');
            }
            
        }
        
        return $this->render('admin/textblock/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

     /**
     * @Route("/delete/{ent_id}", requirements={"ent_id" = "\d+"}, name="textblock_delete")
     */
    public function deleteAction($ent_id){
        $em = $this->getDoctrine()->getManager();
        $textblock = $em->getRepository(TextBlock::class)->find($ent_id);
        
         // Delete picture
        $picture = $textblock->getPicture();
        if (!is_null($picture)){
            $this->get('responsive_image')->deleteImageFiles($picture);
        }
        
        $em->remove($textblock);
        $em->flush();
        
        return $this->redirectToRoute('textblock_list');
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