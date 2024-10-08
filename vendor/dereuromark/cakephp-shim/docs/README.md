# Shim plugin - Usage

## Preconditions
See [Preconditions](Preconditions.md).

## Main shims
These will most likely all be ported over to future releases.

Controller
- [Asserting/Casting](Controller/CastTrait.md)

Model
- [Entity Get/Fail](Model/Entity.md)
- [Nullable](Model/Nullable.md)

## BC shims
The following shims are only in place for 2.x => 3.x and can possibly be removed in the future.

Session
- [Session component/helper](Session/Session.md)

Helper
- [Configure helper](View/Configure.md)
- [Cookie helper](View/Cookie.md)

Routing
- [Inflected route](Routing/Inflected.md)

## Deprecation help
The following deprecation warnings help with 3.x => 4.x:

- [Url helper](View/Url.md)

## ORM
### Table
Extend the Shim plugin Table class to get the functionality:
```php
namespace App\Model\Table;

use Shim\Model\Table\Table as Table;

/**
 * App\Model\Table\MyTable class
 */
class MyTable extends Table {
}
```

In case you are using an app Table class yourself, you can just make this one extend the Shim plugin Table class:
```php
namespace App\Model\Table;

use Shim\Model\Table\Table as ShimTable;

/**
 * App\Model\Table\Table class
 */
class Table extends ShimTable {
}
```
And then all your tables can extend your own app Table class:
```php
namespace App\Model\Table;

/**
 * App\Model\Table\MyTable class
 */
class MyTable extends Table {
}
```

## Controller
### Component
You can extend the Shim plugin Component class to have the controller available inside by default:
```php
namespace Shim\Controller\Component;

use Shim\Controller\Component\Component;

/**
 * App\Controller\Component\MyController class
 */
class MyComponent extends Component {
}
```

## TestSuite
### IntegrationTestSuite
This is mainly a backport from the 3.x class. It provides better testing functionality in 3.x, but in 2.x we can at least
already use the new syntax. This way, when writing a lot of new integration tests, they don't have to be modified anymore once you
finally upgrade. This is a real time safer!

To use the Shim plugin one, simply do the same thing as above:
```php
namespace App\Test\TestCase\Controller;

use Shim\TestSuite\IntegrationTestCase;

/**
 * App\Controller\MyController class
 */
class MyControllerTest extends IntegrationTestCase {
}
```

The syntax is the same as documented in [3.x cookbook](https://book.cakephp.org/3.0/en/development/testing.html#controller-integration-testing).
See examples @ [cakefest app](https://github.com/dereuromark/cakefest/tree/3.0/tests/TestCase/Controller).

### Additional tools
On top, you can use additional debugging tools provided via TestTrait:
```
phpunit --debug
```
to use `$this->isDebug($onlyVeryVerbose = false)` inside tests.
I use this for example to make actual API requests for otherwise mocked requests.
It is a very easy way to switch to "live mode" and verify that the mocks are
still a valid replacement.

```
phpunit -v
phpunit -vv
```
to use `$this->isVerbose($onlyVeryVerbose = false)` inside tests.
This can be useful to conditionally output more debug information.

```
$this->debug($var);
```
Sometimes it can be useful to have additional debug output, which will only be
printed with one of the above verbose flags set. This is a quick convenience wrapper to do so.


### Additional Features
See [Features](Features.md).

### Not Shimmed
See [NotShimmed](NotShimmed.md).
