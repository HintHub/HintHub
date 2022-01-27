<?php

namespace App\Events;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

use Twig\Environment;

class ExceptionListener
{

    private $twig;

    public function __construct(Environment $twig) 
    {
        $this->twig = $twig;
    }
    
    public function onKernelException(ExceptionEvent $event)
    {
        /*TODO - Wir sollten im ExceptionListener quasi nicht error.html.twig rendern - nicht direkt zumindest
                sondern eine dedizierte html haben welches im Easyadminbundle ist
                dann sollten wir die navbar darstellen können*/
        //$twig->render();
    }
}