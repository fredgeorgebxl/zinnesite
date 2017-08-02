<?php

namespace AppBundle\Form;

use AppBundle\Entity\ResponsiveImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use ResponsiveImageBundle\Form\Type\CropFocusType;

class ImageType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $image = $event->getData();
            $form = $event->getForm();
            
            if($image && NULL != $image->getPath()){
                $form->add('crop_coordinates', CropFocusType::class, array('data' => $image, 'label' => 'image.crop_focus', 'translation_domain' => 'App'));
                $form->add('file', FileType::class, array('required' => FALSE, 'label' => 'image.change_file', 'translation_domain' => 'App'));
            } else {
                $form->add('file', FileType::class, array('required' => FALSE, 'label' => 'image.file', 'translation_domain' => 'App'));
            }
        });
        
        $builder
            ->add('title', TextType::class, array('required' => FALSE, 'label' => 'image.title', 'translation_domain' => 'App'))
            ->add('alt', TextType::class, array('required' => FALSE, 'label' => 'image.alt', 'translation_domain' => 'App'));
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
                'data_class' => ResponsiveImage::class,
        ));
    }
}
