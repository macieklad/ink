<?php

namespace Ink\Routing;

use Ink\Routing\Router;

class Route 
{
    /**
     * Uri to which route corresponds
     * 
     * @var string
     */
    protected $uri = '';

    /**
     * Wordpress api regex uri format
     * 
     * @var string
     */
    protected $wpUri = '';

    /** 
     * Http methods the route responds to
     * 
     * @var array
    */
    protected $methods = [];

    /**
     * Keys parsed from uri as data
     * 
     * @var array
     */
    protected $params;

    /**
     * Create new Route instance
     * 
     * @param array $methods
     * @param string $uri
     * @return void
     */
    public function __construct(array $methods, string $uri, $action)
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
    protected function compile() 
    {
        $this->wpUri = preg_replace('/{([a-zA-Z\d_-]+)}/', '(?P<$1>[a-zA-Z\d_-]+)', $this->uri); 
    }

    /**
     * Add prefix to group
     *
     * @param string $prefix
     * @return void
     */
    public function prefix(string $with)
    {
        $this->uri = $with . '/' . trim($this->uri, '/');
    }

    /**
     * Access class property
     *
     * @param string $property
     * @return void
     */
    public function __get(string $property)
    {
        return $this->$property;
    }
}