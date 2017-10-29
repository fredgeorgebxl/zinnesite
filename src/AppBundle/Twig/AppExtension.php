<?php

namespace AppBundle\Twig;

use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
use IrishDan\ResponsiveImageBundle\StyleManager;
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

    public function __construct(StyleManager $styleManager, Helpers $helpers)
    {
        $this->styleManager = $styleManager;
        $this->helpers = $helpers;
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

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}

