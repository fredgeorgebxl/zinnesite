<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType{
    

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder
            ->add('name', TextType::class, array('label' => 'website.contactform.name', 'translation_domain' => 'Front',
                'constraints' => array(
                    new NotBlank(array("message" => "website.contactform.nameerror")),
                )))
            ->add('email', EmailType::class, array('label' => 'website.contactform.email', 'translation_domain' => 'Front',
                'constraints' => array(
                    new NotBlank(array("message" => "website.contactform.emailblankerror")),
                    new Email(array("message" => "website.contactform.emailvaliderror")),
                )))
            ->add('subject', TextType::class, array('label' => 'website.contactform.subject', 'translation_domain' => 'Front',
                'constraints' => array(
                    new NotBlank(array("message" => "website.contactform.subjecterror")),
                )))
            ->add('message', TextareaType::class, array('label' => 'website.contactform.message', 'attr' => array('rows' => 10), 'translation_domain' => 'Front',
                'constraints' => array(
                    new NotBlank(array("message" => "website.contactform.messageerror")),
                )))
            ->add('save', SubmitType::class, array('label' => 'website.contactform.send', 'translation_domain' => 'Front'));
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'error_bubbling' => true,
            'timed_spam' => true,
            'timed_spam_min' => 3,
            'timed_spam_max' => 1500,
            'timed_spam_message' => 'website.contactform.timeerror',
            'honeypot' => true,
            'honeypot_field' => 'email_address',
            'honeypot_use_class' => false,
            'honeypot_hide_class' => 'hidden',
            'honeypot_message' => 'website.contactform.invalid',
        ));
    }
}