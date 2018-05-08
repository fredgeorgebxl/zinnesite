<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Gallery;
use AppBundle\Entity\ResponsiveImage;
use AppBundle\Form\GalleryType;
use AppBundle\Form\GalleryImageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;

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
        $repository = $entityManager->getRepository(Gallery::class);
        $qb = $repository->createQueryBuilder('gal');
        $queryEvents = $qb
                ->where('gal.homeslide != 1')
                ->orWhere($qb->expr()->isNull('gal.homeslide'))
                ->orderBy('gal.date', 'DESC')
                ->getQuery();
        $galleries = $queryEvents->getResult();

        return $this->render('admin/gallery/index.html.twig', ['galleries' => $galleries]);
    }
    
    /**
     * @Route("/new/{param}", name="gallery_new")
     */
    public function newAction(Request $request, $param = 'nothing')
    {

       $gallery = new Gallery();
       $gallery->setPublished(TRUE);

       $gallery->setDateCreated(new \DateTime());
       $gallery->setDateModified(new \DateTime());
       if ($param == 'homeslide'){
           $gallery->setHomeslide(TRUE);
       } else {
           $gallery->setHomeslide(FALSE);
       }

       $form = $this->createForm(GalleryType::class,$gallery);
       $form->handleRequest($request);
       
       if($form->isSubmitted() && $form->isValid()){
            $gallery = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($gallery);
            $em->flush();
            
            if($form->get('addimages')->isClicked()){
                return $this->redirectToRoute('gallery_addimages', ['ent_id' => $gallery->getId()]);
            } else {
                if($gallery->getHomeslide()){
                    return $this->redirectToRoute('admin_home');
                } else {
                    return $this->redirectToRoute('gallery_list');
                }
                
            }
        }

        return $this->render('admin/gallery/new.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }
    
    /**
     * @Route("/edit/{ent_id}", requirements={"ent_id" = "\d+"}, name="gallery_edit")
     */
    public function editAction($ent_id, Request $request){

        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)->find($ent_id);
        
        if (!$gallery) {
            throw $this->createNotFoundException(
                'No gallery found for id '.$ent_id
            );
        }
        
        $images = $gallery->getPictures();
        
        $form = $this->createForm(GalleryType::class,$gallery);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $gallery->setDateModified(new \DateTime());
            $em->flush();
            
            if($form->get('addimages')->isClicked()){
                return $this->redirectToRoute('gallery_addimages', ['ent_id' => $ent_id]);
            } elseif ($form->get('edit_images')->isClicked()) {
                return $this->redirectToRoute('gallery_editimages', ['ent_id' => $ent_id]);
            } elseif ($gallery->getHomeslide()) {
                return $this->redirectToRoute('admin_home');
            } else {
                return $this->redirectToRoute('gallery_list');
            }
            
        }
        
        return $this->render('admin/gallery/edit.html.twig', array(
            'form' => $form->createView(),
            'images' => $images,
        ));
    }
    
    /**
     * @Route("/addimages/{ent_id}", requirements={"ent_id" = "\d+"}, name="gallery_addimages")
     */
    public function addfileAction($ent_id, Request $request){

        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)->find($ent_id);
        
        if (!$gallery) {
            throw $this->createNotFoundException(
                'No gallery found for id '.$ent_id
            );
        }
        
        $builder = $this->createFormBuilder();
            $builder->setAction($this->generateUrl('gallery_addimages', ['ent_id' => $ent_id]))
            ->add('file', FileType::class);
            if ($gallery->getHomeslide()){
                $builder->add('back_home', SubmitType::class, array('label' => 'gallery.save', 'translation_domain' => 'App'));
            } else {
                $builder->add('edit_gallery', SubmitType::class, array('label' => 'gallery.editgallery', 'translation_domain' => 'App'));
            }
            $builder->add('edit_images', SubmitType::class, array('label' => 'gallery.editimages', 'translation_domain' => 'App'));
            $form = $builder->getForm();
        
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
            
            if($form->has('edit_gallery') && $form->get('edit_gallery')->isClicked()){
                return $this->redirectToRoute('gallery_edit', ['ent_id' => $ent_id]);
            }
            if($form->get('edit_images')->isClicked()){
                return $this->redirectToRoute('gallery_editimages', ['ent_id' => $ent_id]);
            }
            if($form->has('back_home') && $form->get('back_home')->isClicked()){
                return $this->redirectToRoute('admin_home');
            }
        }
        
        return $this->render('admin/gallery/addimages.html.twig', array(
            'form' => $form->createView(),
            'gallery' => $gallery,
        ));
    }
    
    /**
     * @Route("/editimages/{ent_id}", requirements={"ent_id" = "\d+"}, name="gallery_editimages")
     */
    public function editimagesAction($ent_id, Request $request){

        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)->find($ent_id);
        
        if (!$gallery) {
            throw $this->createNotFoundException(
                'No gallery found for id '.$ent_id
            );
        }
        $form = $this->createFormBuilder($gallery)
                ->add('title', HiddenType::class)
                ->add('pictures', CollectionType::class, array('error_bubbling' => FALSE, 'allow_delete' => true, 'entry_type' => GalleryImageType::class, 'entry_options' => array('attr' => array('class' => 'image-box'))))
                ->add('save', SubmitType::class, array('label' => 'gallery.save', 'translation_domain' => 'App'))
                ->getForm();
        
        $originalImages = new ArrayCollection();
        
        foreach ($gallery->getPictures() as $image) {
            $originalImages->add($image);
        }
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            
            foreach ($originalImages as $image) {
                if (false === $gallery->getPictures()->contains($image)) {
                    $gallery->removePicture($image);
                    $this->get('responsive_image')->deleteImageFiles($image, TRUE, TRUE);
                    $em->remove($image);
                }
            }
            
            $em->persist($gallery);
            $em->flush();
            
            if ($gallery->getHomeslide()){
                return $this->redirectToRoute('admin_home');
            } else {
                return $this->redirectToRoute('gallery_edit', ['ent_id' => $ent_id]);
            }
        }
        
        return $this->render('admin/gallery/editimages.html.twig', array(
            'form' => $form->createView(),
            'homeslide' => $gallery->getHomeslide(),
            'ent_id' => $ent_id,
        ));
    }
    
    /**
     * @Route("/cropimage/{ent_id}/{img_id}", requirements={"ent_id" = "\d+", "img_id" = "\d+"}, name="gallery_cropimage")
     */
    public function cropimageAction($ent_id, $img_id, Request $request){
        $em = $this->getDoctrine()->getManager();
        $image = $em->getRepository(ResponsiveImage::class)->find($img_id);
        
        if (!$image) {
            throw $this->createNotFoundException(
                'No image found for id '.$img_id
            );
        }
        $form = $this->createFormBuilder($image)
                ->add('crop_coordinates', \IrishDan\ResponsiveImageBundle\Form\CropFocusType::class, array('data' => $image, 'label' => 'image.crop_focus', 'translation_domain' => 'App'))
                ->add('save', SubmitType::class, array('label' => 'image.save', 'translation_domain' => 'App'))
                ->getForm();
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            
            $this->get('responsive_image')->deleteImageFiles($image, FALSE, TRUE);
            
            $em->persist($image);
            $em->flush();

            return $this->redirectToRoute('gallery_editimages', ['ent_id' => $ent_id]);
            
        }
        
        return $this->render('admin/gallery/cropimage.html.twig', array(
            'form' => $form->createView(),
        ));
        
    }

     /**
     * @Route("/delete/{ent_id}", requirements={"ent_id" = "\d+"}, name="gallery_delete")
     */
    public function deleteAction($ent_id){
        
        $em = $this->getDoctrine()->getManager();
        $gallery = $em->getRepository(Gallery::class)->find($ent_id);
        $images = $gallery->getPictures();
        foreach ($images as $image){
            $this->get('responsive_image')->deleteImageFiles($image, TRUE, TRUE);
            $em->remove($image);
        }
        
        $em->remove($gallery);
        $em->flush();
        
        return $this->redirectToRoute('gallery_list');
    }
}