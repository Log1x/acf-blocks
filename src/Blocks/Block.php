<?php

namespace App\Blocks;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use function Roots\view;

abstract class Block {
    /**
     * The display name of the block.
     *
     * @var string
     */
    protected $name = '';

    /**
     * The description of the block.
     *
     * @var string
     */
    protected $description = '';

    /**
     * The category this block belongs to.
     *
     * @var string
     */
    protected $category = '';

    /**
     * The icon of this block.
     *
     * @var string
     */
    protected $icon = '';

    /**
     * An array of keywords the block will be found under.
     *
     * @var array
     */
    protected $keywords = [];

    /**
     * An array of post types the block will be available to.
     *
     * @var array
     */
    protected $post_types = ['post', 'page'];

    /**
     * The default display mode of the block that is shown to the user.
     *
     * @var string
     */
    protected $mode = 'preview';

    /**
     * The block alignment class.
     *
     * @var string
     */
    protected $align = '';

    /**
     * Features supported by the block.
     *
     * @var array
     */
    protected $supports = [];

    /**
     * The blocks status.
     *
     * @var boolean
     */
    protected $enabled = true;

    /**
     * The block prefix.
     *
     * @var string
     */
    protected $prefix = 'acf/';

    /**
     * Compose the block.
     *
     * @return void
     */
    public function compose()
    {
        if (! $this->register() || ! function_exists('acf')) {
            return;
        }

        collect($this->register())->each(function ($value, $name) {
            $this->{$name} = $value;
        });

        $this->slug = Str::slug($this->name);
        $this->fields = $this->fields();

        if (! $this->enabled) {
            return;
        }

        add_action('init', function () {
            acf_register_block([
                'name'            => $this->slug,
                'title'           => $this->name,
                'description'     => $this->description,
                'category'        => $this->category,
                'icon'            => $this->icon,
                'keywords'        => $this->keywords,
                'post_types'      => $this->post_types,
                'mode'            => $this->mode,
                'align'           => $this->align,
                'supports'        => $this->supports,
                'enqueue_assets'  => [$this, 'assets'],
                'render_callback' => [$this, 'view'],
            ]);

            if (! empty($this->fields)) {
                if (! Arr::has($this->fields, 'location.0.0')) {
                    Arr::set($this->fields, 'location.0.0', [
                        'param' => 'block',
                        'operator' => '==',
                        'value' => $this->prefix . $this->slug,
                    ]);
                }

                acf_add_local_field_group($this->fields);
            }
        }, 20);
    }

    /**
     * Path for the block.
     *
     * @return string
     */
    protected function path()
    {
        return dirname((new \ReflectionClass($this))->getFileName());
    }

    /**
     * URI for the block.
     *
     * @return string
     */
    protected function uri($path = '')
    {
        return str_replace(
            get_theme_file_path(),
            get_theme_file_uri(),
            home_url($path)
        );
    }

    /**
     * View used for rendering the block.
     *
     * @return \Roots\view
     */
    public function view()
    {
        echo view($this->path() . "/views/{$this->slug}.blade.php", $this->with());
    }

    /**
     * Assets used when rendering the block.
     *
     * @return void
     */
    public function assets()
    {
        if (file_exists($style = $this->path() . "/views/{$this->slug}.css")) {
            wp_enqueue_style($this->prefix . $this->slug, $this->uri($style), false, null);
        }

        if (file_exists($script = $this->path() . "/views/{$this->slug}.js")) {
            wp_enqueue_script($this->prefix . $this->slug, $this->uri($script), null, null, true);
        }
    }

    /**
     * Data to be passed to the block before registering.
     *
     * @return array
     */
    public function register()
    {
        return [];
    }

    /**
     * Fields to be attached to the block.
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }

    /**
     * Data to be passed to the rendered block.
     *
     * @return array
     */
    public function with()
    {
        return [];
    }
}
