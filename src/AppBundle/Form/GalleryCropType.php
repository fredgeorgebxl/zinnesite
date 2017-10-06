<?php

namespace AppBundle\Form;

use AppBundle\Entity\ResponsiveImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use IrishDan\ResponsiveImageBundle\Form\CropFocusType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;


class GalleryCropType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $image = $event->getData();
            $form = $event->getForm();
            $form->add('crop_coordinates', CropFocusType::class, array('data' => $image, 'label' => 'image.crop_focus', 'translation_domain' => 'App'));
        });
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ResponsiveImage::class,
        ));
    }
}