<?php
 
class API {
        public $routes = [];

        private function normalize_uri($uri){
            $normalized_uri = str_replace(' ', '', $uri);
            $normalized_uri = preg_replace('/\/+/', '/', $normalized_uri);
            $normalized_uri = preg_replace('/\{([A-Za-z]|[0-9])+\}/', '{}', $normalized_uri);
            if ($normalized_uri=='/'){
                return $normalized_uri;
            }
            if(substr($normalized_uri, -1) == '/'){
                $normalized_uri = substr($normalized_uri, 0, -1);;
            }
            return $normalized_uri;
        }

        private function searchRoute($old_route){
            $levels = explode('/', $old_route);
            $verb = array_shift($levels);
            if ($levels[0]=="") // if is root level
                return null;
            $possible_routes = preg_grep('/'.$verb.'/', array_keys($this->routes));
            $route = array(
                'key' => $verb,
                'args' => []
            );
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
            return $route;
        }

        private function addToRoutes($verb, $uri, $function){
            $uri = $this->normalize_uri($uri);
            $this->routes[$verb.$uri]= $function;
        }

        public function get($uri, $function){
            $this->addToRoutes('GET',$uri, $function);
        }
        public function post($uri, $function){
            $this->addToRoutes('POST',$uri, $function);
        }
        public function put($uri, $function){
            $this->addToRoutes('PUT',$uri, $function);
        }
        public function delete($uri, $function){
            $this->addToRoutes('DELETE',$uri, $function);
        }

        public function route($server) {
            $uri = $server['REQUEST_URI'];
            $uri = $this->normalize_uri($uri);
            $verb = $server['REQUEST_METHOD'];
            $route = $verb.$uri;
            if (array_key_exists($route, $this->routes)){
                $this->routes[$route]();
            }else{
                $route = $this->searchRoute($route);
                if($route !== NULL){
                    call_user_func_array($this->routes[$route['key']], $route['args']);
                }
            }
            return;
        }
}
?>