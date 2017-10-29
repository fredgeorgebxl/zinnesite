<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use AppBundle\Entity\ResponsiveImage;
use AppBundle\Form\RegistrationType;
use AppBundle\Form\UserEditType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
  * @Route("/admin/user")
  */
class UserController extends Controller{
    /**
     * @Route("/", name="user_list")
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(User::class)->findBy([], ['firstname' => 'asc']);

        return $this->render('admin/user/index.html.twig', ['users' => $users]);
    }
    
    /**
     * @Route("/new", name="user_new")
     */
    public function newAction(Request $request)
    {
       $user = new User();
       $form = $this->createForm(RegistrationType::class,$user);
       $form->handleRequest($request);
       
        if($form->isSubmitted() && $form->isValid()){
            
            $em = $this->getDoctrine()->getManager();
            
            $user = $form->getData();
            $user->addRole('ROLE_USER');
            $user->setUsername($user->getEmail());
            
            
            // Upload picture
            $file = $form->get('picture')->get('file')->getData();
            $alt = $form->get('picture')->get('alt')->getData();
            $title = $form->get('picture')->get('title')->getData();
            if($file){
                $picture = $this->initPicture($file, $alt, $title);
                $user->setPicture($picture);
            } else {
                $user->setPicture(NULL);
            }
            
            $em->persist($user);
            $em->flush();
            
            if($form->get('picture')->has('add_image') && $form->get('picture')->get('add_image')->isClicked()){
                return $this->redirectToRoute('user_edit', ['user_id' => $user->getId()]);
            } else {
                return $this->redirectToRoute('user_list');
            }
        }
        
        return $this->render('admin/user/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @Route("/edit/{user_id}", requirements={"user_id" = "\d+"}, name="user_edit")
     */
    public function editAction($user_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($user_id);
        $username = $user->getUsername();
        
        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id '.$user_id
            );
        }
        
        $form = $this->createForm(UserEditType::class,$user);
        $form->handleRequest($request);
        $picture = $user->getPicture();
        
        if($form->isSubmitted() && $form->isValid()){
            $user->setUsername($username);
            
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
                $user->setPicture(NULL);
            }
            
            // Reset picture to NULL if the picture has no path to avoid an SQL error
            if(!is_null($picture) && empty($picture->getPath())){
                $user->setPicture(NULL);
            }
            
            $em->flush();
            
            if(($form->get('picture')->has('remove_image') && $form->get('picture')->get('remove_image')->isClicked()) || ($form->get('picture')->has('add_image') && $form->get('picture')->get('add_image')->isClicked())){
                return $this->redirectToRoute('user_edit', ['user_id' => $user_id]);
            } else {
                return $this->redirectToRoute('user_list');
            }
        }
        
        return $this->render('admin/user/edit.html.twig', array(
            'form' => $form->createView(),
            'picture' => $picture,
        ));
    }
    
    /**
     * @Route("/delete/{user_id}", requirements={"user_id" = "\d+"}, name="user_delete")
     */
    public function deleteAction($user_id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($user_id);
        
        if (!in_array('ROLE_SUPER_ADMIN', $user->getRoles())){
            
            // Delete picture
            $picture = $user->getPicture();
            if (!is_null($picture)){
                $this->get('responsive_image')->deleteImageFiles($picture);
            }
            
            $em->remove($user);
            $em->flush();
        }
        
        return $this->redirectToRoute('user_list');
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
