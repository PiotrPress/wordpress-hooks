# WordPress Hooks

This library uses [PHP Attributes](https://www.php.net/manual/en/language.attributes.overview.php) (introduced in PHP version `8.0`) to automagically add/remove [WordPress Hooks](https://developer.wordpress.org/plugins/hooks/) ([Filters](https://codex.wordpress.org/Plugin_API/Filter_Reference) and [Actions](https://codex.wordpress.org/Plugin_API/Action_Reference)) to/from functions and methods.

**Note:** The library supports PHP >= `7.4` version.

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
#[ Action( string $name, int $priority = 10 ) ]
#[ Filter( string $name, int $priority = 10 ) ]
```

### Functions

```php
Hooks::add( object $object = null, string $callback = '', PiotrPress\CacherInterface $cache = null ) : void
Hooks::remove( object $object = null, string $callback = '', PiotrPress\CacherInterface $cache = null ) : void
```

## Examples

### Hooks::add/remove( $object )

If `object` argument is passed and `callback` is omitted, then all hooks from object are added or removed.

```php
use PiotrPress\WordPress\Hooks;
use PiotrPress\WordPress\Hooks\Action;
use PiotrPress\WordPress\Hooks\Filter;

class Example {
    public function add_hooks() {
        Hooks::add( $this );
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

$example = new Example();
$example->add_hooks();

Hooks::remove( $example );
```

This is an equivalent to:

```php
$example = new Example();

add_action( 'init', [ $example, 'example_init' ] );
add_filter( 'the_title', [ $example, 'example_the_title' ], 1, 2 );

remove_action( 'init', [ $example, 'example_init' ] );
remove_filter( 'the_title', [ $example, 'example_the_title' ], 1, 2 );
```

**Note:** `Hooks::add/remove()` methods can be called from the method, or even outside the object.

### Hooks::add/remove( $object, $callback )

If `object` and `callback` arguments are passed, then only hooks for this method are added or removed.

```php
use PiotrPress\WordPress\Hooks;
use PiotrPress\WordPress\Hooks\Action;
use PiotrPress\WordPress\Hooks\Filter;

class Example {
    public function add_hooks() {
        Hooks::add( $this, 'example_init' );
        Hooks::add( $this, 'example_the_title' );
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

$example = new Example();
$example->add_hooks();

Hooks::remove( $example, 'example_init' );
Hooks::remove( $example, 'example_the_title' );
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

If `object` argument is omitted and `callback` is passed, then only hooks for this function are added or removed.

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

## Cache

Optionally, you can pass a cache object, which must implement [PiotrPress\CacherInterface](https://github.com/PiotrPress/cacher/blob/master/src/CacherInterface.php) interface, as a third `cache` argument to `Hooks::add/remove()` methods.

This will cache the result of `Hooks::get()` method, which provides a list of hooks for a given object, method or function using [Reflection API](https://www.php.net/manual/en/book.reflection.php), so caching its result can significantly improve the performance.

### Example

```php
use PiotrPress\Cacher;
use PiotrPress\WordPress\Hooks;
use PiotrPress\WordPress\Hooks\Action;
use PiotrPress\WordPress\Hooks\Filter;

class Example {
    #[ Action( 'init' ) ]
    public function example_init() : void {
        // do something
    }

    #[ Filter( 'the_title', 1 ) ]
    public function example_the_title( string $post_title, int $post_id ) : string {
        // do something
    }
}

$example = new Example();
$cache = new Cacher( '.hooks' );

Hooks::add( object: $example, cache: $cache );
Hooks::remove( object: $example, cache: $cache );
```

**Note:** You can use simple file-based cache, which is provided by [PiotrPress\Cacher](https://github.com/PiotrPress/cacher) library distributed with this library.

## Kudos

Inspirations, feedback, ideas and feature requests provided by:

- [Jakub Mikita](https://github.com/jakubmikita)
- [Sebastian Pisula](https://github.com/sebastianpisula)
- [Mateusz Gbiorczyk](https://github.com/gbiorczyk)
- [Krzysztof Grabania](https://github.com/Dartui)
- [Dominik Kawula](https://github.com/domkawula)
- [Jacek Sławiński](https://github.com/jacekslawinski)

## Troubleshooting

**Note:** Named arguments not working in PHP < `8.0` version.

## Requirements

PHP >= `7.4` version.

## License

[GPL3.0](license.txt)