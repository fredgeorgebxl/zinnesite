<?php

namespace AppBundle\Twig;

use IrishDan\ResponsiveImageBundle\ResponsiveImageInterface;
//use IrishDan\ResponsiveImageBundle\ResponsiveImageManager;
use IrishDan\ResponsiveImageBundle\StyleManager;

/**
 * Class AppExtension
 *
 * @package AppBundle\Twig
 */
class AppExtension extends \Twig_Extension
{
    private $styleManager;

    public function __construct(StyleManager $styleManager)
    {
        $this->styleManager = $styleManager;
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

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}

