<?php

namespace Ink\Routing;

class Route
{

    /**
     * Route action, callable or string
     * pointing to a controller method
     *
     * @var null
     */
    public $action = null;
    /**
     * Route module
     *
     * @var string
     */
    public $module = 'v1';

    /**
     * Uri to which route corresponds
     * 
     * @var string
     */
    public $uri = '';

    /**
     * Wordpress api regex uri format
     * 
     * @var string
     */
    public $wpUri = '';

    /** 
     * Http methods the route responds to
     * 
     * @var array
     */
    public $methods = [];

    /**
     * Keys parsed from uri as data
     * 
     * @var array
     */
    public $params;

    /**
     * Create new Route instance
     * 
     * @param array  $methods
     * @param string $uri
     * 
     * @return void
     */
    public function __construct(array $methods, string $uri)
    {
        $this->uri = $uri;
        $this->wpUri = '';
        $this->methods = $methods;
        $this->params = [];
    }

    /**
     * Prepare route for wordpress registration
     *
     * @return void
     */
    public function prepare()
    {
        $this->extractParams();
        $this->compile();
    }

    /**
     * Extract route params from it's uri
     * 
     * @return void
     */
    public function extractParams()
    {
        preg_match_all('/\{(.*?)\}/', $this->uri, $matches);

        $this->params = $matches[1];
    }

    /**
     * Merge attributes onto route
     *
     * @param array $attributes
     * 
     * @return void
     */
    public function mergeAttributes(array $attributes)
    {
        foreach ($attributes as $attribute => $value) {
            if ($attribute == 'action') {
                $this->action = $value;
            } else {
                $this->$attribute($value);       
            }
        }
    }

    /**
     * Converts uri to WP_API regex match
     *
     * @return void
     */
    public function compile() 
    {
        $this->wpUri = preg_replace(
            '/{([a-zA-Z\d_-]+)}/',
            '(?P<$1>[a-zA-Z\d_-]+)',
            $this->uri
        );
    }

    /**
     * Add prefix to group
     *
     * @param string $with
     * 
     * @return void
     */
    public function prefix(string $with)
    {
        $this->uri = '/' . trim($with, '/') . '/' . trim($this->uri, '/');
    }

    /**
     * Change route module
     *
     * @param string $name
     *
     * @return void
     */
    public function module(string $name)
    {
        $this->module = $name;
    }

}
