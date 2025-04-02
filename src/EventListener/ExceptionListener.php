<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Default response data
        $response = [
            'error' => $exception->getMessage(),
        ];

        // Default status code
        $statusCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        // If it's an HTTP exception, use its status code
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }

        // Create a JSON response
        $event->setResponse(new JsonResponse($response, $statusCode));
    }
}