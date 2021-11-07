# WordPress Hooks

The library allows using [PHP Attributes](https://www.php.net/manual/en/language.attributes.overview.php) (introduced in PHP version `8.0`) and/or PHP Annotations (PHP Doc Comments) and/or objects' methods names prefixed by `filter_` or `action_` to automagically add [WordPress Hooks](https://developer.wordpress.org/plugins/hooks/) ([Filters](https://codex.wordpress.org/Plugin_API/Filter_Reference) and [Actions](https://codex.wordpress.org/Plugin_API/Action_Reference)) to objects' methods.

## Installation

```console
composer require piotrpress/wordpress-hooks
```

## Usage

### Attributes

```php
#[Action('tag',priority)]
#[Filter('tag',priority)]
```

Where hooks' parameters are:

1. **tag** - a required string value, which is the hook name
2. **priority** - an optional integer value, where default is `10` 
3. **function_to_add** - a method with the `Action` and/or `Filter`
4. **accepted_args** - a number of method's parameters (sets automagically)

#### Basic Example

```php
require __DIR__ . '/vendor/autoload.php';

use PiotrPress\WordPress\Hooks\Filter;
use PiotrPress\WordPress\Hooks\Action;
use PiotrPress\WordPress\Hooks\Attributes as AttributesHooks;

class Example {
    use AttributesHooks;

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

**NOTE:** `Attributes` supports PHP `7.4` but due to forward compatibility with PHP `8.0` including code below is mandatory:

```php
use PiotrPress\WordPress\Hooks\Filter;
use PiotrPress\WordPress\Hooks\Action;
```

### Annotations

```php
@action tag priority
@filter tag priority
```

Where hooks' parameters are:

1. **tag** - a required string value, which is the hook name
2. **priority** - an optional integer value, where default is `10`
3. **function_to_add** - a method with the `action` and/or `filter`
4. **accepted_args** - a number of method's parameters (sets automagically)

#### Basic Example

```php
require __DIR__ . '/vendor/autoload.php';

use PiotrPress\WordPress\Hooks\Annotations as AnnotationsHooks;

class Example {
    use AnnotationsHooks;

    public function __construct() {
        $this->add_hooks();
    }

    /**
     * @action init
     */
    public function example_init() {
        // do something
    }

    /**
     * @filter the_content 11
     */
    public function example_the_content( $content ) {
        // do something
    }
}

new Example();
```

**NOTE:** `Annotations` syntax is compatible with [DocHooks](https://github.com/micropackage/dochooks).

### Methods

```php
function action_tag_priority()
function filter_tag_priority()
```

Where hooks' parameters are:

1. **tag** - a required string value, which is the hook name
2. **priority** - an optional integer value, where default is `10`
3. **function_to_add** - a method with the `action_` or `filter_` prefix
4. **accepted_args** - a number of method's parameters (sets automagically)

#### Basic Example

```php
require __DIR__ . '/vendor/autoload.php';

use PiotrPress\WordPress\Hooks\Annotations as AnnotationsHooks;

class Example {
    use AnnotationsHooks;

    public function __construct() {
        $this->add_hooks();
    }

    public function action_init() {
        // do something
    }

    public function filter_the_content_11( $content ) {
        // do something
    }
}

new Example();
```

**NOTE:** `Methods` syntax is compatible with [Clearcode Framework](https://github.com/ClearcodeHQ/wordpress-framework).

## Advanced Example

```php
require __DIR__ . '/vendor/autoload.php';

use PiotrPress\WordPress\Hooks\Filter;
use PiotrPress\WordPress\Hooks\Action;
use PiotrPress\WordPress\Hooks\Annotations as AnnotationsHooks;
use PiotrPress\WordPress\Hooks\Attributes as AttributesHooks;
use PiotrPress\WordPress\Hooks\Methods as MethodsHooks;

class Example {
    use AnnotationsHooks, AttributesHooks, MethodsHooks {
        AttributesHooks::add_hooks insteadof AnnotationsHooks;
        AttributesHooks::add_hooks insteadof MethodsHooks;
        AnnotationsHooks::add_hooks as add_annotations_hooks;
        AttributesHooks::add_hooks as add_attributes_hooks;
        MethodsHooks::add_hooks as add_methods_hooks;
    }

    public function __construct() {
        $this->add_annotations_hooks();
        $this->add_attributes_hooks();
        $this->add_methods_hooks();
    }

    /**
     * @action init
     */
    #[Action('init')]
    public function action_init() {
        // do something
    }

    /**
     * @filter the_content 11
     */
    #[Filter('the_content', 11)]
    public function filter_the_content_11( $content ) {
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

## Requirements

* Branch `4.x` supports PHP >= `7.4` version.
* Branch `3.x` supports PHP >= `8.0` version.
* Branch `2.x` supports PHP ^`7.4` version.

## License

[GPL3.0](license.txt)