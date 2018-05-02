<?php

namespace AppBundle\Twig;

use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
use IrishDan\ResponsiveImageBundle\StyleManager;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Helpers;


/**
 * Class AppExtension
 *
 * @package AppBundle\Twig
 */
class AppExtension extends \Twig_Extension
{
    private $styleManager;
    private $helpers;

    public function __construct(StyleManager $styleManager, Helpers $helpers, ObjectManager $em)
    {
        $this->styleManager = $styleManager;
        $this->helpers = $helpers;
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('class_styled_image', [$this, 'generateClassStyledImage'], [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new \Twig_SimpleFunction('embed_video', [$this, 'embedYoutubeVideo'], [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new \Twig_SimpleFunction('styled_image_url', [$this, 'styledImageUrl'], [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
            new \Twig_SimpleFunction('text_block', [$this, 'insertTextBlock'], [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                ]
            ),
        ];
    }


    public function generateClassStyledImage(\Twig_Environment $environment, ResponsiveImageInterface $image, $styleName, $classes)
    {
        $this->styleManager->setImageStyle($image, $styleName);

        return $environment->render('ResponsiveImageBundle::img.html.twig', [
            'image' => $image,
            'classes' => $classes,
        ]);
    }
    
    public function embedYoutubeVideo(\Twig_Environment $environment, \AppBundle\Entity\Video $video){
        $video_id = $this->helpers->getYoutubeId($video->getLink());
        return $environment->render('default/video.html.twig', [
           'video_id' => $video_id, 
        ]);
    }
    
    public function styledImageUrl(\Twig_Environment $environment, ResponsiveImageInterface $image, $styleName){
        $this->styleManager->setImageStyle($image, $styleName);
        return $image->getStyle();
    }
    
    public function insertTextBlock(\Twig_Environment $environment, $blockname){
        $textBlock = $this->em->getRepository(\AppBundle\Entity\TextBlock::class)->findOneByName($blockname);
        return $environment->render('default/textblock.html.twig', [
            'textblock' => $textBlock,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}

