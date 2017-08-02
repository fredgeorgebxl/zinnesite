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
        $users = $entityManager->getRepository(User::class)->findAll();

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
            
            $picture = new ResponsiveImage();
            $picture->setPath('');
            $em->persist($picture);
            
            // Upload picture
            $file = $form->get('picture')->get('file')->getData();
            if($file){
                $picture->setFile($file);
                $this->get('responsive_image.uploader')->upload($picture);
            }
            $picture_title = $form->get('picture')->get('title')->getData();
            $picture_alt = $form->get('picture')->get('alt')->getData();
            if (!empty($picture_title)){
                $picture->setTitle($picture_title);
            }
            if(!empty($picture_alt)){
                $picture->setAlt($picture_alt);
            }
            
            $user->setPicture($picture);
            $em->persist($user);
            $em->flush();
            
            return $this->redirectToRoute('user_list');
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
        
        $picture = $user->getPicture();
        $form = $this->createForm(UserEditType::class,$user);
        $form->handleRequest($request);
        
        if(is_null($picture)){
            $picture = new ResponsiveImage();
            $picture->setPath('');
            $em->persist($picture);
            $user->setPicture($picture);
        }
        
        // Upload picture
        $file = $form->get('picture')->get('file')->getData();
        if($file){
            
            // Delete current file if exists
            if(!empty($picture->getPath())){
                $this->get('responsive_image')->deleteImageFiles($picture);
            }
            
            // Upload new file
            $picture->setFile($file);
            $this->get('responsive_image.uploader')->upload($picture);
        }
        
        if($form->isSubmitted() && $form->isValid()){
            $user->setUsername($username);
            $em->flush();
            return $this->redirectToRoute('user_list');
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
                $em->remove($picture);
            }
            
            $em->remove($user);
            $em->flush();
        }
        
        return $this->redirectToRoute('user_list');
    }
}
