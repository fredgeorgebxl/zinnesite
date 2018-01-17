<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @Route("/admin")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="admin_home")
     */
    public function indexAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $homepage_slideshow = $entityManager->getRepository(\AppBundle\Entity\Gallery::class)->findOneBy(['homeslide' => 1]);
        
        return $this->render('admin/default/index.html.twig', ['slideshow' => $homepage_slideshow]);
    }
    
    /**
     * @Route("/clearcache", name="clear_cache")
     */
    public function clearCache($env = 'prod', $debug = true)
    {
        $kernel = new \AppKernel($env, $debug);
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'cache:clear'
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);
        
        return $this->redirectToRoute('admin_home');
    }
}
