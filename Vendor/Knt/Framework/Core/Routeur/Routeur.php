<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Knt\Framework\Core\Routeur;

/**
 * Description of Routeur
 *
 * @author Aurelien
 */
class Routeur {
    
    private $_routes = array();
    
    //put your code here
    public function addRoute(RouteInterface $route) {
        $this->_routes[$route->getUri()] = $route;
    }
    
    public function exists($uri) {
        return array_key_exists($uri, $this->_routes);
    }
    
    public function getRoute($uri) {
        
        if (!$this->exists($uri)) {
            throw new \OutOfBoundsException("No route exists for the uri '$uri'");
        }
        return $this->_routes[$uri];
        
    }
}
