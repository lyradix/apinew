<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();
        
        // Log the exception for debugging
        error_log("[Exception Listener] Exception caught: " . $exception->getMessage());
        error_log("[Exception Listener] Exception class: " . get_class($exception));
        error_log("[Exception Listener] Request path: " . $request->getPathInfo());
        error_log("[Exception Listener] Request method: " . $request->getMethod());
        
        // Only convert exceptions to JSON responses for API routes or AJAX requests
        // This will prevent interfering with normal web form submissions
        if ($this->isApiRequest($request)) {
            $response = new JsonResponse([
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(), // Include trace for debugging
            ]);

            if ($exception instanceof HttpExceptionInterface) {
                $response->setStatusCode($exception->getStatusCode());
            } else {
                $response->setStatusCode(JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }

            $event->setResponse($response);
        }
        // For non-API requests, let Symfony handle the exception normally
    }
    
    private function isApiRequest(Request $request): bool
    {
        // Consider a request as an API request if:
        // 1. It starts with /api/
        // 2. It expects a JSON response
        // 3. It's an AJAX request
        
        $pathInfo = $request->getPathInfo();
        $acceptHeader = $request->headers->get('Accept');
        $isXmlHttpRequest = $request->isXmlHttpRequest();
        
        return (
            strpos($pathInfo, '/api/') === 0 ||
            strpos($acceptHeader, 'application/json') !== false ||
            $isXmlHttpRequest
        );
    }
}