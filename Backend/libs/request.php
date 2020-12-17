<?php
class Request{
    public $params = [];
    public $middlewares = [];

    /**
     * Create a request object with the given given options
     *
     * @param array $options an associative array, where each key contains an array. It can contains the following keys:
     * ['params', 'middlewares']
     * It is an optional parameter.
     */
    function __construct($options=[]){
        if(array_key_exists('params', $options))
            $this->add_params($options['params']);
        if(array_key_exists('middlewares', $options))
            $this->add_middleware($options['middlewares']);
    }

    /**
     * Add the given params
     *
     * @param array $params an associative array of params, in the form [param_name => mixed_value]
     * @return void
     */
    private function add_params($params){
        foreach($params as $key => $value){
            $this->params[$key] = $value;
        }
    }

    /**
     * Get the param associated with the given key
     *
     * @param mixed $key the key to search.
     * @return null|mixed the value if the key exists, otherwhise null.
     */
    public function getParam($key){
        if(array_key_exists($key, $this->params))
            return $this->params[$key];
        return null;
    }

    /**
     * Add the given middlewares
     *
     * @param array $middlewares an associative array of middlewares, in the form [middleware_name => middleware_callable]
     * @return null|mixed the value if the key exists, otherwhise null.
     */
    private function add_middleware($middlewares){
        foreach($middlewares as $key => $value){
            $this->middlewares[$key] = $value;
        }
    }

    /**
     * Get the middleware callable associated with the given key
     *
     * @param mixed $key the key to search.
     * @return null|mixed the callable if the key exists, otherwhise null.
     */
    public function getMiddleware($key){
        if(array_key_exists($key, $this->middlewares))
            return $this->middlewares[$key];
        return null;
    }

    /**
     * Checks if there is at least one param
     *
     * @return boolean true if there is at least one param, otherwhise false.
     */
    public function hasParams(){
        return count($this->params)>0;
    }

    /**
     * Return the number of params
     *
     * @return boolean the number of params.
     */
    public function numParams(){
        return count($this->params);
    }
    
}