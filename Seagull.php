<?php

/**
 *
 */
class Seagull
{
    /**
     * The separator to use for paths
     */
    protected $separator;

    /**
     * the complete configuration array
     */
    protected $config = array();

    /** 
     * Constructor
     *
     * @param array $data
     *    default configuration
     * @param string $separator
     *    the separator to use for paths
     */
    public function __construct($data = array(), $separator = '.')
    {
        $this->separator = $separator;
        if(!empty($data)) {
            $this->config = $data;
        }
    }

    /**
     * Recursive function to get an element from, or set an element in the config by its path
     *
     * @access protected
     *
     * @param string $path
     *    path to the element in the array (path.to.element)
     * @param array|string &$conf
     *    the configuration or part of the configuration (on recursion)
     * @param mixed $newValue (optional)
     *    the new value to set in the config on the specified path
     *
     * @return mixed
     *    the value found in the config at the specified path, or null if the path doesn't exist
     */
    protected function conf($path, &$conf, $newValue = null) {

        if(!$path) return $this->config;

        $sep = $this->separator;
        $path = explode($sep, $path);
        $first = array_shift($path);

        if(isset($conf[$first])) {

            // there's more left on the path, keep following it
            if(count($path)) {
                goto recurse;
            }
            
            if($newValue === '[[seagull-delete]]') {
                // for deleting values
                unset($conf[$first]);
                return;
            } elseif($newValue !== null) {
                $conf[$first] = $newValue;
            }

            return $conf[$first];
        } elseif($newValue !== null) {
            // a new piece of path, create it
            if(count($path)) {
                $conf[$first] = array();
                goto recurse;
            }
            return $conf[$first] = $newValue;
        }

        return null;

        recurse:
        return $this->conf(implode($sep, $path), $conf[$first], $newValue); // <--- RECURSE!!!
    }


    /**
     * Merge override values into the default config
     *
     * @param array $merge
     *    the array to merge into the defaults
     * @param array $config
     *    optional, only needed to recurse
     *
     * @return array
     *    the original config, with the new values merged into it
     */
    protected function doMerge(array $merge, $config = null)
    {
        $config = $config !== null ? $config : $this->config;

        foreach($merge as $key => $value) {
            $config[$key] = array_key_exists($key, $config) && is_array($value)
                ? $this->doMerge($merge[$key], $config[$key]) // <--- RECURSE!!!
                : $value;
        }
        return $config;
    } 

    /**
     * Merge override values into the default config
     *
     * @param array $merge
     *    the array to merge into the defaults
     *
     * @return Seagull
     *    the current instance, for chaining
     */
    public function merge($merge)
    {
        $this->config = $this->doMerge($merge);
        return $this;
    }

    /**
     * Get a value from the config, from a specific path.
     *
     * @param string $path
     *    path to the element in the config (path.to.element)
     *
     * @return mixed
     *    the value found in the config at the specified path, or null if the path doesn't exist
     */
    public function get($path = null)
    {
        return $this->conf($path, $this->config, null);
    }

    /**
     * Set a value in the config, on a specific path.
     *
     * @param string $path
     *    path to the element in the config (path.to.element)
     * @param mixed $newValue (optional)
     *    the new value to set in the config on the specified path
     *
     * @return Seagull
     *    the current instance, for chaining
     */
    public function set($path, $value)
    {
        $this->conf($path, $this->config, $value);
        return $this;
    }

    /**
     * Delete a value on a path
     *
     * @param string $path
     *    the path to remove from the config
     *
     * @return Seagull
     *    the current instance, for chaining
     */
    public function delete($path)
    {
        $this->conf($path, $this->config, '[[seagull-delete]]');
        return $this;
    }
}