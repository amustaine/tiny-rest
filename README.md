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

## Transfer Objects

Transfer object is a tiny wrapper for HTTP request. The transfer object SHOULD NOT contain entities or convert some user data into complex custom objects. The general idea is having a validatable object which will contain pure user data

### Annotation

#### Property

```
@Property(name="foo", mapped=true, type="datetime")
```

`name` - By default the name equals to the name of the property but can be changed for the cases when there is manual mapping needed

`mapped` - whether the value will be set to Transfer object or not. Default: *true*

`type` - There are cases when the value should be casted to a certain type, for example using Transfer Object as filter in a repository. This types are available for type cast: `string`, `integer`, `float`, `array`, `datetime`, `boolean`. Default value is *string*

#### Mapping

The annotation should be used for describing the transfer strategy with the entity. By default the column name equals to the name of the property.
The annotation cannot be used without `@Property()` annotation

```
@Mapping(column="someEntityField", mapped=true)
```

`column` - By default the name equals to the name of the property but can be changed for the cases when there is manual mapping needed

`mapped` - Can be set as *false* for cases when the property should not transfer it's data to the entity. Default: *true*

#### Events

##### OnObjectHydrated

```
/**
 * @OnObjectHydrated(method="setTimestamp")
 */
class SomeClass implements TransferObjectInterface
{
    private $timestamp;

    public function setTimestamp()
    {
        $this->timestamp = time(); 
    }
}
```

Triggers after object hydration but before validation

##### OnObjectValid

```
/**
 * @OnObjectHydrated(callback={"OtherClass", "getRandomNumber"})
 */
class SomeClass implements TransferObjectInterface
{
    public $randomNumber;
}

class OtherClass
{
    public static function getRandomNumber(SomeClass $object)
    {
        $object->randomNumber = mt_rand(1, 10); 
    }
}
```

Triggers after object validation

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
