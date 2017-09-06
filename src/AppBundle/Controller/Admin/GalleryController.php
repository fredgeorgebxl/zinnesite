<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Gallery;
use AppBundle\Entity\ResponsiveImage;
use AppBundle\Form\GalleryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
  * @Route("/admin/gallery")
  */
class GalleryController extends Controller
{
    /**
     * @Route("/", name="gallery_list")
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $galleries = $entityManager->getRepository(Gallery::class)->findBy([], ['datecreated' => 'desc']);

        return $this->render('admin/gallery/index.html.twig', ['galleries' => $galleries]);
    }
    
    /**
     * @Route("/new", name="gallery_new")
     */
    public function newAction(Request $request)
    {

       $gallery = new Gallery();
       $gallery->setPublished(TRUE);

       $gallery->setDateCreated(new \DateTime());
       $gallery->setDateModified(new \DateTime());

       $form = $this->createForm(GalleryType::class,$gallery);
       $form->handleRequest($request);
       
       if($form->isSubmitted() && $form->isValid()){
            $gallery = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($gallery);
            $em->flush();
            
            return $this->redirectToRoute('gallery_list');
        }

        return $this->render('admin/gallery/new.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    /**
     * @Route("/edit/{gal_id}", requirements={"gal_id" = "\d+"}, name="gallery_edit")
     */
    public function editAction($gal_id, Request $request){

        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)->find($gal_id);
        
        if (!$gallery) {
            throw $this->createNotFoundException(
                'No gallery found for id '.$gal_id
            );
        }
        
        $form = $this->createForm(GalleryType::class,$gallery);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $gallery->setDateModified(new \DateTime());
            $em->flush();
            
            if($form->get('addimages')->isClicked()){
                return $this->redirectToRoute('gallery_addimages', ['gal_id' => $gal_id]);
            } else {
                return $this->redirectToRoute('gallery_list');
            }
            
        }
        
        return $this->render('admin/gallery/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/addimages/{gal_id}", requirements={"gal_id" = "\d+"}, name="gallery_addimages")
     */
    public function addfileAction($gal_id, Request $request){

        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)->find($gal_id);
        
        if (!$gallery) {
            throw $this->createNotFoundException(
                'No gallery found for id '.$gal_id
            );
        }
        
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('gallery_addimages', ['gal_id' => $gal_id]))
            ->add('file', FileType::class)
            ->add('edit_gallery', SubmitType::class, array('label' => 'gallery.edit_gallery', 'translation_domain' => 'App'))
            ->add('edit_images', SubmitType::class, array('label' => 'gallery.edit_images', 'translation_domain' => 'App'))
            ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            
            
            $em = $this->getDoctrine()->getManager();
            
            // Upload picture
            $data = $form->getData();
            $file = $data["file"];
            if($file){
                $picture = new ResponsiveImage();
                $picture->setPath('');
                $em->persist($picture);
                $picture->setFile($file);
                $picture->setGallery($gallery);
                try{
                    $this->get('responsive_image.uploader')->upload($picture);
                }catch(Exception $e){
                    return new JsonResponse(['error' => $e->getMessage()]);
                }
                $em->flush();
                return new JsonResponse(['success' => true]);
            }
            
            if($form->get('edit_gallery')->isClicked()){
                return $this->redirectToRoute('gallery_edit', ['gal_id' => $gal_id]);
            }
            if($form->get('edit_images')->isClicked()){
                return $this->redirectToRoute('gallery_edit', ['gal_id' => $gal_id]);
            }
        }
        
        return $this->render('admin/gallery/addimages.html.twig', array(
            'form' => $form->createView(),
            'gallery' => $gallery,
        ));
    }

     /**
     * @Route("/delete/{gal_id}", requirements={"gal_id" = "\d+"}, name="gallery_delete")
     */
    public function deleteAction($gal_id){
        
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)->find($gal_id);
        $em->remove($gallery);
        $em->flush();
        
        return $this->redirectToRoute('gallery_list');
    }
}