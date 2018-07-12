<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceListener
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $maintenance = $this->container->hasParameter('app.maintenance') ? $this->container->getParameter('app.maintenance') : false;
        $debug = in_array($this->container->get('kernel')->getEnvironment(), ['dev']);

        if ($maintenance && !$debug) {
            $engine = $this->container->get('twig');
            $template = $engine->render('maintenance.html.twig');
            $event->setResponse(new Response($template, 503));
            $event->stopPropagation();
        }
    }
}
