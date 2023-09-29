# WordPress Hooks

The library allows using [PHP Attributes](https://www.php.net/manual/en/language.attributes.overview.php) (introduced in PHP version `8.0`) to automagically add [WordPress Hooks](https://developer.wordpress.org/plugins/hooks/) ([Filters](https://codex.wordpress.org/Plugin_API/Filter_Reference) and [Actions](https://codex.wordpress.org/Plugin_API/Action_Reference)) to objects' methods.

## Installation

```console
composer require piotrpress/wordpress-hooks
```

## Example

```php
require __DIR__ . '/vendor/autoload.php';

use PiotrPress\WordPress\Hook;

class Example {
    public function __construct() {
        Hook::up( $this );
    }

    #[ Hook( 'init' ) ]
    public function example_init() {
        // do something
    }

    #[ Hook( 'the_content', 11 ) ]
    public function example_the_content( $content ) {
        // do something
    }
}

new Example();
```

This is an equivalent to:

```php
$example = new Example();

add_action( 'init', [ $example, 'action_init' ] );
add_filter( 'the_content', [ $example, 'filter_the_content_11' ], 11, 1 );
```

## Usage

```php
#[ Hook( string $hook_name, int $priority = 10 ) ]
```

Where hook's parameters are:

1. **hook_name** - a required string value, which is the hook name
2. **priority** - an optional integer value, where default is `10`

```php
Hook::up( object $object ) : void
```

Note: Method can be called in or out of the constructor.

## Requirements

PHP >= `8.0` version.

## License

[GPL3.0](license.txt)