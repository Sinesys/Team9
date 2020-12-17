<?php
require_once('request.php');
class REST {
        private $options=[
            'good_status_code' => 200,
            'good_message' => null,
            'bad_status_code' => 501,
            'bad_message' => null,
            'accept_preflight_requests' => true
        ];
        private $routes = [];
        private $middlewares = [];
        private static $instance = null;

        /**
         * Returns the Singleton istance of the class.
         *
         * @return REST the istance.
         */
        public static function getInstance()
        {
            static $instance = null;
            if (null === $instance) {
                $instance = new REST();
            }
    
            return $instance;
        }
        private function __construct(){}
        private function __clone(){}

        /**
         * Set the options
         *
         * @param array $options an array containg the values for the options.
         * The options that can be setted are:
         * 'good_status_code': set the default status code when the callback returns true (default 200);
         * 'good_message': set the default message the callback returns true (no message by default);
         * 'bad_status_code': set the default status code when the callback returns false, or when the route is not registred (default 501);
         * 'bad_message': set the default message the callback returns false, or when the route is not registred (no message by default);,
         * 'accept_preflight_requests': if it is set to true, the preflight requests are automatically accepted (default true).
         * 
         * @return void
         */
        public function setOptions($options = []){
            foreach($options as $key=>$value)
                $this->options[$key] = $value;
        }

        private function searchRoute($old_route){
            $route = ['key' => null, 'args' => []];
            if (array_key_exists($old_route, $this->routes)){
                $route['key'] = $old_route;
                return $route;
            }

            $levels = explode('/', $old_route);
            $verb = array_shift($levels);
            if ($levels[0] == "") // if is root level
                return null;
            $possible_routes = preg_grep('/'.$verb.'/', array_keys($this->routes));
            $route['key'] = $verb;
            $uri = '';

            foreach($levels as $level){
                $temp_routes = preg_grep('/^'.$verb.$uri.'\/'.$level.'(\/|[A-Za-z]|[0-9]|{})*$/', $possible_routes);
                if (!empty($temp_routes)){
                    $uri = $uri.'\/'.$level;
                    $possible_routes = $temp_routes;
                    continue;
                }

                $temp_routes = preg_grep('/^'.$verb.$uri.'\/{}'.'(\/|[A-Za-z]|[0-9]|{})*$/', $possible_routes);
                if (empty($temp_routes)){
                    return NULL;
                }
                $uri = $uri.'\/{}';
                $possible_routes = $temp_routes;
                array_push($route['args'], $level);

            }
            $route['key'] = $verb.str_replace("\\","",$uri);
            if (!array_key_exists($route['key'], $this->routes))
                return null;
            
            return $route;
        }

        private function normalize_uri($uri){
            if ($uri=='/')
                return $uri;
            if (substr($uri, -1) != '/')
                $uri = '/'.$uri;
            
            $normalized_uri = str_replace(' ', '', $uri);
            $normalized_uri = preg_replace('/\/+/', '/', $normalized_uri);
            $normalized_uri = preg_replace('/\{([A-Za-z0-9])+\}/', '{}', $normalized_uri);

            if(substr($normalized_uri, -1) == '/')
                $normalized_uri = substr($normalized_uri, 0, -1);

            return $normalized_uri;
        }

        private function uri_params($uri, $onlyname = false){
            $exp_uri = explode('?', $uri);
            $uri = $exp_uri[0];
            $params = [];
            if(count($exp_uri)<2)
                return ['uri'=>$uri, 'params' => $params];

            $str_params = $exp_uri[1];
            $keys_values = explode('&', $str_params);
            if($onlyname === true)
                return ['uri'=>$uri, 'params' => $keys_values];
            
            $params = [];
            foreach($keys_values as $param){
                $key_value = explode('=', $param);
                if(count($key_value)>=2)
                    $params[$key_value[0]] = $key_value[1];
            }
            return ['uri'=>$uri, 'params' => $params];
        }

        private function addToRoutes($verb, $uri, $function, $middlewares){
            $uri_params = $this->uri_params($uri, true);
            $uri = $this->normalize_uri($uri_params['uri']);

            $route_middlewares=[];
            if(gettype($middlewares)==="string")
                $middlewares = [$middlewares];

            foreach($middlewares as $middleware)
                if(array_key_exists($middleware, $this->middlewares))
                    $route_middlewares[]=$middleware;
            
            $this->routes[$verb.$uri] = [
                'function' => $function,
                'params' => $uri_params['params'],
                'middlewares' => $route_middlewares
            ];
        }

        /**
         * Register the given route (when the given uri is request with a GET verb)
         *
         * @param string $uri the uri to be registred.
         * @param callable $function the callable object to be called.
         * @param array $middlewares the array containing the keys of the middlewares previously registred.
         * @return void
         */
        public function get($uri, $function, $middlewares=[]){
            $this->addToRoutes('GET', $uri, $function, $middlewares);
        }
        /**
         * Register the given route (when the given uri is request with a POST verb)
         *
         * @param string $uri the uri to be registred.
         * @param callable $function the callable object to be called.
         * @param array $middlewares the array containing the keys of the middlewares previously registred.
         * @return void
         */
        public function post($uri, $function, $middlewares=[]){
            $this->addToRoutes('POST', $uri, $function, $middlewares);
        }
        /**
         * Register the given route (when the given uri is request with a PUT verb)
         *
         * @param string $uri the uri to be registred.
         * @param callable $function the callable object to be called.
         * @param array $middlewares the array containing the keys of the middlewares previously registred.
         * @return void
         */
        public function put($uri, $function, $middlewares=[]){
            $this->addToRoutes('PUT', $uri, $function, $middlewares);
        }
        /**
         * Register the given route (when the given uri is request with a DELETE verb)
         *
         * @param string $uri the uri to be registred.
         * @param callable $function the callable object to be called.
         * @param array $middlewares the array containing the keys of the middlewares previously registred.
         * @return void
         */
        public function delete($uri, $function, $middlewares=[]){
            $this->addToRoutes('DELETE', $uri, $function, $middlewares);
        }

        /**
         * Add a middleware.
         *
         * @param string $key the key with whom the middleware is registred.
         * @param callable $function the callable object to be called.
         * @return void
         */
        public function middleware($key, $function){
            $this->middlewares[$key] = $function;
            return $key;
        }

        /**
         * Redirect the client request to the matching route (if there is a match)
         * 
         * By default, the route matched is the one with less arguments. So, between these two registred routes:
         *  '/route/1';
         *  '/route/{}'.
         * If the request uri is '/route/1',  the first one will be matched.
         * 
         * @param array $server the variable $_SERVER.
         * @return void
         */
        public function route($server) {
            $verb = $server['REQUEST_METHOD'];
            $uri = $server['REQUEST_URI'];
            $uri_params = $this->uri_params($uri);
            $uri = $this->normalize_uri($uri_params['uri']);
            $response = null;
            $route = $this->searchRoute($verb.$uri);

            if($route !== NULL){
                $fun = $this->routes[$route['key']]['function'];
                $args = $route['args'];
                $acceptable_params = $this->routes[$route['key']]['params'];
                $actual_params = [];
                foreach($acceptable_params as $param){
                    if(array_key_exists($param, $uri_params['params'])){
                        $actual_params[$param] = $uri_params['params'][$param];
                    }
                }
                
                $options = [
                    'params' => $actual_params,
                    'middlewares' => []
                ];
                foreach($this->routes[$route['key']]['middlewares'] as $middleware){
                    $result = $this->middlewares[$middleware]();
                    if($result instanceof Response){
                        $this->handle_response($result);
                        return;
                    }
                    $options['middlewares'][$middleware] = $result;
                }

                $request = new Request($options);
                $fun = $fun->bindTo($request);
                $response = call_user_func_array($fun, $args);
            }
            
            if($verb=='OPTIONS' and $route===NULL and $this->options['accept_preflight_requests'])
                http_response_code(200);
            else
                $this->handle_response($response);
        }

        private function handle_response($response){
            if($response==null){
                if($this->options['bad_message']!=null)
                    echo $this->options['bad_message'];
                http_response_code($this->options['bad_status_code']);
            }elseif($response==true and !($response instanceof Response)){
                if($this->options['good_message']!=null)
                    echo $this->options['good_message'];
                http_response_code($this->options['good_status_code']);
            }else{
                if(!$response->isFile())
                    echo $response->getData();
                http_response_code($response->getCode());
            }
            return;
        }
}
?>