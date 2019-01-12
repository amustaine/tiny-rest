# Tiny-rest

Heyyy! I just released this library and currently writing some docs for it, so please wait a while.

But if you are eager and cannot wait:

## Installation

```
composer require russbalabanov/tiny-rest
```

## Configuration

Please modify your `services.yaml` file to register this two services

```
...

TinyRest\Pagination\PaginationFactory:
TinyRest\RequestHandler:
```

### Handling exceptions and sending API Errors

When you don't want to bother much with validation errors, you may want to go for this solution.


```
class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();

        if ($e instanceof ValidationException) {
            $error = $this->createValidationMessage($e);
            
            $response = new JsonResponse($error, 400);
            $response->headers->set('Content-Type', 'application/problem+json');
            $event->setResponse($response);
            
            return $event;
        }

        return $event;
    }

    private function createValidationMessage(ValidationException $exception) : string
    {
        $violation = $exception->getViolationList()->get(0);
        
        return sprintf('%s: %s', $violation->getPropertyPath(), $violation->getMessage());
    }
}
```


WARNING: This project is currently in aplha stage, which means minimal risk of BC does exist.
