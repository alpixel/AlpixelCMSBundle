<?php

namespace Alpixel\Bundle\CMSBundle\Controller;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\Routing\Annotation\Route;

class ExceptionController extends BaseController
{
    protected $exceptionTemplate;

    public function __construct(\Twig_Environment $twig, $debug, $exceptionTemplate)
    {
        $this->exceptionTemplate = $exceptionTemplate;
        parent::__construct($twig, $debug);
    }


    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
        $showException = $request->attributes->get('showException', $this->debug); // As opposed to an additional parameter, this maintains BC

        $code = $exception->getStatusCode();

        if ($showException) {
            $statusCode = Response::HTTP_ACCEPTED;
        } else {
            $statusCode = $code;
        }

        return new Response($this->twig->render(
            (string) $this->findTemplate($request, $request->getRequestFormat(), $code, $showException),
            [
                'status_code'    => $code,
                'status_text'    => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                'exception'      => $exception,
                'logger'         => $logger,
                'currentContent' => $currentContent,
            ]
        ), $statusCode);
    }

    /**
     * @param Request $request
     * @param string  $format
     * @param int     $code          An HTTP response status code
     * @param bool    $showException
     *
     * @return string
     */
    protected function findTemplate(Request $request, $format, $code, $showException)
    {
        if (!$showException) {
            if ($this->templateExists($this->exceptionTemplate)) {
                return $this->exceptionTemplate;
            }
        }

        return parent::findTemplate($request, $format, $code, $showException);
    }
}
