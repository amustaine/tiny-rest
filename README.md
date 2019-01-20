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

## Usage

### Create, Update, List and Pagination

The idea of all operations was to allow user to care less about the trivial and usual stuff and focus on business logic

#### Create

To create an entity out of request you will need only three agruments. Request object, TransferObject (as a tiny wrapper for the request) and an Entity object.

```
/**
 * @var Product $product
 */
$product = $handler->create($request, new ProductTransferObject(), new Product());

$em->persit($product);
$em->flush();
```

#### Update

The update method is almost the same as create() with one exception. If you are doing a PUT HTTP method all the ORM fields (except @Id) will be cleared

```
$handler->update($request, new ProductTransferObject(), $product);

$em->flush();
```

#### Pagination

```
use TinyRest\Pagination\PaginatedCollection

/**
 * @var PaginatedCollection $collection
 */
$collection = $handler->getPaginatedList($request, new UserTransferObject(), $provider);
```

### Providers for collections

##### ORM

###### DBAL

##### NativeQuery

##### Entity

##### Array

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
