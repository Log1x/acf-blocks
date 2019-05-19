# ACF Blocks

ACF Blocks is a small package for Sage 10 to assist you in easily creating Gutenberg Blocks with Advanced Custom Fields.

**Note: This is a work in progress.**

## Installation

```sh
$ composer require log1x/blocks
```

Add `App\Blocks\BlockServiceProvider::class` to the providers array in `config/app.php`.

## Usage

- Create a directory to store your Blocks.

```
app/Blocks
└── Example # Your Block
    ├── Example.php # Where you register your block, fields, and data passed to it's view.
    └── views
        ├── example.blade.php # Block view
        ├── example.css       # Block CSS file (optional)
        └── example.js        # Block JS file (optional)
```

- Register your Block, attach it's fields, and provide data for your view (similar to Sage 10's Composers).

```php
# Blocks/Example/Example.php
<?php

namespace App\Blocks\Example;

use App\Blocks\Block;
use StoutLogic\AcfBuilder\FieldsBuilder;

class Example extends Block {
    /**
     * Data to be passed to the block before registering.
     * @see https://www.advancedcustomfields.com/resources/acf_register_block_type/
     *
     * @return array
     */
    public function register()
    {
        return [
            'name' => 'Example',
            'description' => 'Lorem ipsum',
            'category' => 'formatting',
        ];
    }

    /**
     * Fields to be attached to the block.
     *
     * @return array
     */
    public function fields()
    {
        $example = new FieldsBuilder('example');

        $example
            ->setLocation('block', '==', 'acf/example');

        $example
            ->addText('label')
            ->addTextarea('description')
            ->addRepeater('items')
                ->addText('item')
            ->endRepeater();

        return $example->build();
    }

    /**
     * Data to be passed to the rendered block.
     *
     * @return array
     */
    public function with()
    {
        return [
            'label' => $this->label(),
            'description' => $this->description(),
            'items' => $this->items(),
        ];
    }

    /**
     * Returns the label field.
     *
     * @return string
     */
    public function label()
    {
        return get_field('label');
    }

    /**
     * Returns the description field.
     *
     * @return string
     */
    public function description()
    {
        return get_field('description');
    }

    /**
     * Returns the items field.
     *
     * @return array
     */
    public function items()
    {
        return get_field('items') ?? [];
    }
}
```

- Create your Block's view.

```php
# Blocks/Example/views/example.blade.php
<div class="bg-blue-600 text-white p-4 mb-4">
  <h2 class="text-3xl mb-2">{{ $label }}</h2>
  <p>{{ $description }}</p>
  @if ($items)
    <ul>
      @foreach ($items as $item)
        <li>{{ $item['item'] }}</li>
      @endforeach
    </ul>
  @endif
</div>
```

- Load your Block inside of `config/blocks.php`.

```php
'blocks' => [
    App\Blocks\Example\Example::class,
],
```

and that's it!

If a CSS or JS file exists inside of your block's `views` folder, they will automatically be enqueued with your block on both the frontend and backend.

## License

ACF Blocks is provided under the [MIT License](https://github.com/log1x/acf-blocks/blob/master/LICENSE.md).
