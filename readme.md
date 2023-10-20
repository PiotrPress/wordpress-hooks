# WordPress Hooks

This library uses [PHP Attributes](https://www.php.net/manual/en/language.attributes.overview.php) (introduced in PHP version `8.0`) to automagically add/remove [WordPress Hooks](https://developer.wordpress.org/plugins/hooks/) ([Filters](https://codex.wordpress.org/Plugin_API/Filter_Reference) and [Actions](https://codex.wordpress.org/Plugin_API/Action_Reference)) to/from functions and methods.

## Installation

```console
$ composer require piotrpress/wordpress-hooks
```

### Load

```php
require __DIR__ . '/vendor/autoload.php';
```

## Usage

### Attributes

```php
#[ Action( string $hook_name, int $priority = 10 ) ]
#[ Filter( string $hook_name, int $priority = 10 ) ]
```

### Functions

```php
Hooks::add( object $object = null, string $callback = '' ) : void
Hooks::remove( object $object = null, string $callback = '' ) : void
```

## Examples

### Hooks::add/remove( $object )

If `object` argument is passed and `callback` is omitted, then all hooks from object are added or removed.

```php
use PiotrPress\WordPress\Hooks;
use PiotrPress\WordPress\Hooks\Action;
use PiotrPress\WordPress\Hooks\Filter;

class Example {
    public function __construct() {
        Hooks::add( $this );
        Hooks::remove( $this );
    }

    #[ Action( 'init' ) ]
    public function example_init() : void {
        // do something
    }

    #[ Filter( 'the_title', 1 ) ]
    public function example_the_title( string $post_title, int $post_id ) : string {
        // do something
    }
}

new Example();
```

This is an equivalent to:

```php
$example = new Example();

add_action( 'init', [ $example, 'example_init' ] );
add_filter( 'the_title', [ $example, 'example_the_title' ], 1, 2 );

remove_action( 'init', [ $example, 'example_init' ] );
remove_filter( 'the_title', [ $example, 'example_the_title' ], 1, 2 );
```

### Hooks::add/remove( $object, $callback )

If `object` and `callback` arguments are passed, then only hook for this method is added or removed.

```php
use PiotrPress\WordPress\Hooks;
use PiotrPress\WordPress\Hooks\Action;
use PiotrPress\WordPress\Hooks\Filter;

class Example {
    public function __construct() {
        Hooks::add( $this, 'example_init' );
        Hooks::add( $this, 'example_the_title' );
        
        Hooks::remove( $this, 'example_init' );
        Hooks::remove( $this, 'example_the_title' );
    }

    #[ Action( 'init' ) ]
    public function example_init() : void {
        // do something
    }

    #[ Filter( 'the_title', 1 ) ]
    public function example_the_title( string $post_title, int $post_id ) : string {
        // do something
    }
}

new Example();
```

This is an equivalent to:

```php
$example = new Example();

add_action( 'init', [ $example, 'example_init' ] );
add_filter( 'the_title', [ $example, 'example_the_title' ], 1, 2 );

remove_action( 'init', [ $example, 'example_init' ] );
remove_filter( 'the_title', [ $example, 'example_the_title' ], 1, 2 );
```

### Hooks::add/remove( callback: $callback )

If `object` argument is omitted and `callback` is passed, then only hook for this function is added or removed.

```php
use PiotrPress\WordPress\Hooks;
use PiotrPress\WordPress\Hooks\Action;
use PiotrPress\WordPress\Hooks\Filter;

#[ Action( 'init' ) ]
public function example_init() : void {
    // do something
}

#[ Filter( 'the_title', 1 ) ]
public function example_the_title( string $post_title, int $post_id ) : string {
    // do something
}

Hooks::add( callback: 'example_init' );
Hooks::add( callback: 'example_the_title' );

Hooks::remove( callback: 'example_init' );
Hooks::remove( callback: 'example_the_title' );
```

This is an equivalent to:

```php
add_action( 'init', 'example_init' );
add_filter( 'the_title', 'example_the_title', 1, 2 );

remove_action( 'init', 'example_init' );
remove_filter( 'the_title', 'example_the_title', 1, 2 );
```

**Note:** `Hooks::add/remove()` methods can be called from in or out of the constructor method, or even outside the object.

## Requirements

PHP >= `8.0` version.

## License

[GPL3.0](license.txt)