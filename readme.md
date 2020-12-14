# WordPress Hooks

The library allows using [PHP Attributes](https://www.php.net/manual/en/language.attributes.overview.php) (introduced in PHP version `8`) to automagically add [WordPress Hooks](https://developer.wordpress.org/plugins/hooks/) ([Filters](https://codex.wordpress.org/Plugin_API/Filter_Reference) and [Actions](https://codex.wordpress.org/Plugin_API/Action_Reference)) to objects' methods.

## Installation

```console
composer require piotrpress/wordpress-hooks
```

## Usage

### PHP Attributes

```php
#[Action('tag',priority)]
#[Filter('tag',priority)]
```

Where hooks' parameters are:

1. **tag** - a required string value, which is the hook name
2. **priority** - an optional integer value, where default is `10` 
3. **function_to_add** - a method with the Action and/or Filter attribute
4. **accepted_args** - a number of method's parameters

## Example

```php
require __DIR__ . '/vendor/autoload.php';

use PiotrPress\WordPress\Hooks\Action;
use PiotrPress\WordPress\Hooks\Filter;
use PiotrPress\WordPress\Hooks\Hooks;

class Example {
    use Hooks;

    public function __construct() {
        $this->add_hooks();
    }

    #[Action('init')]
    public function example_init() {
        // do something
    }

    #[Filter('the_content', 11)]
    public function example_the_content( $content ) {
        // do something
    }
}

new Example();
```

This is an equivalent to:

```php
$example = new Example();

add_action( 'init', [ $example, 'example_init' ] );
add_filter( 'the_content', [ $example, 'example_the_content' ], 11, 1 );
```

## License

GPL3.0