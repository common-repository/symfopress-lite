<?php

namespace Netandreus\SymfopressBundle\EventListener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ControllerListener
{
    protected $extension;

    public function __construct(\Netandreus\SymfopressBundle\Twig\Extension\DemoExtension $extension)
    {
        $this->extension = $extension;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if($event->getException()
            && get_class($event->getException()) == 'Symfony\Component\HttpKernel\Exception\NotFoundHttpException'
            && strpos($event->getException()->getMessage(), 'No route found for ') === 0) {
                throw new \Exception('SF_NOT_FOUND_EXCEPTION', 404); // take control back to wp plugin
        }
    }

    // for /demo/secured/login
    public function onKernelController(FilterControllerEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $this->extension->setController($event->getController());
        }
    }
}
