# Future enhancements for CiviProxy
At the moment there are a few future enhancements, bug fixes and small suggested changes, as you can see on the [issue list](https://github.com/systopia/CiviProxy/issues).

!!! tip
    If you want to report bugs or suggest future enhancements please do so on the [issue list](https://github.com/systopia/CiviProxy/issues).

## Create your plugin

You can create a new composer package which should require `systopia/civiproxy`.

Create a `src` directory in your package. Create a file `MyDummyPlugin.php` in the `src` directory.

```php

// You can give your plugin any namespace.
// As long as it matches the autoloader in your composer.json file
namespace Systopia/CiviproxyDummyPlugin

use Systopia\CiviProxy\Api\JsonResponse;
use Systopia\CiviProxy\Events\finishRedirect;
use Systopia\CiviProxy\Events\prepareRedirect;
use Systopia\CiviProxy\Events\redirectError;
use Systopia\CiviProxy\PluginInterface;

class MyDummyPlugin implments PluginInterface {

  /**
   * Get subscribed events.
   * 
   * @return array
   */
  public function getSubscribedEvents() {
    // You can implement any or all of those events.
    return [
      prepareRedirect::class => 'onPrepareRedirect',
      redirectError::class => 'onRedirectError',
      finishRedirect::class => 'onFinishRedirect',
    ];
  }

  /**
   * Get api's.
   * 
   * @return array
   */
  public function getApiActionDefinitions() {
    // You can implement any of those api's.
    return [
      'my-dummy-api' => ['onMyDummyApi', ['id']],
    ];
  }

  public function onMyDummyApi($id) {
    return new JsonResponse(['data' => []]);
  }

  public function onPrepareRedirect(prepareRedirect $event) {
    // Do something
  }

  public function onRedirectError(redirectError $event) {
    // Do something
  }

  public function onFinishRedirect(finishRedirect $event) {
    // Do something
  }

}

```

The above code implements three event listeners. And a CiviProxy api.

Now create a composer.json file in your package directory.

```json
{
    "name": "systopia/civiproxy-dummy-plugin",
    "require": {
        "systopia/civiproxy": "*",
    },
    "autoload": {
      "psr-4": {
        "Systopia\\CiviproxyDummyPlugin\\":"src"
      }
    }
}
```