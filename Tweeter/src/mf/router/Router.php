<?php

namespace mf\router;
use \mf\auth\Authentification;
class Router extends AbstractRouter {

    public function __construct(){
        parent::__construct();
    }

    public function run(){
        if(array_key_exists($this->http_req->path_info, self::$routes)){
            $url = $this->http_req->path_info;
            $auth = new Authentification();
            if($auth->checkAccessRight(self::$routes[$url][2])){
                $c_name = self::$routes[$url][0];
                $m_name = self::$routes[$url][1];
            }
            else{
                $c_name = self::$routes[self::$aliases['default']][0];
                $m_name = self::$routes[self::$aliases['default']][1];
            }
        }
        else
        {
            $c_name = self::$routes[self::$aliases['default']][0];
            $m_name = self::$routes[self::$aliases['default']][1];
        }
            $c = new $c_name;
            $c->$m_name();
        
    }
    public function urlFor($route_name, $param_list=[]){
        
        if(isset(self::$aliases[$route_name])){
            $url_alias = self::$aliases[$route_name];

            $url = $this->http_req->script_name.$url_alias;

            if($param_list != null){
                $url = $url."?";
                foreach($param_list as $param){ 
                    $url = $url.$param[0]."=".$param[1]/*."&"*/;
                }
            }
            return ($url); 
        }
    }
    public function setDefaultRoute($url){
        self::$aliases['default']=$url;
    }
    public function addRoute($name, $url, $ctrl, $mth, $access){
            self::$routes[$url]=[$ctrl, $mth, $access];
            self::$aliases[$name]=$url;
    }
    public static function executeRoute($alias){
        if(isset(self::$aliases[$alias])){
            $url = self::$aliases[$alias];
            $c_name = self::$routes[$url][0];
            $m_name = self::$routes[$url][1];
        }
        else
        {
            $c_name = self::$routes[self::$aliases['default']][0];
            $m_name = self::$routes[self::$aliases['default']][1];
        }
            
            $c = new $c_name;
            $c->$m_name();
        
    }
}