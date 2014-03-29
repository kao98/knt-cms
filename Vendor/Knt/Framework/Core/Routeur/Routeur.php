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
    
    private $_automatic = false;
    private $_routes    = array();
    
    public function __construct($automatic = false) {
        $this->_automatic = $automatic;
    }
    
    //put your code here
    public function addRoute(RouteInterface $route) {
        $this->_routes[$route->getUri()] = $route;
    }
    
    public function exists($uri, $path = VIEWS_PATH, $extension = VIEWS_EXTENSION) {
        
        if (array_key_exists($uri, $this->_routes)) {
            return true;
        } elseif ($this->_automatic) {
            return $this->_automaticExists($uri, $path, $extension);
        }
        
        return false;
        
    }
    
    public function getRoute($uri) {
        
        if (!$this->exists($uri)) {
            throw new \OutOfBoundsException("No route exists for the uri '$uri'");
        }
        return $this->_routes[$uri];
        
    }
    
    public function _automaticExists($uri, $path, $extension) {
        
        $path = rtrim($path, '\\/');
        
        if (is_dir($path)) {

            $uriParts       = explode('/', trim($uri, '/'));
            $methodName     = array_pop($uriParts);
            $componentName  = implode('/', $uriParts);
            $fileName       = $componentName . $extension;

            if (is_file($path . '/' . $fileName)) {
                $this->addRoute(new Route($uri, $componentName, $methodName));
                return true;
            }
        
        }
        
        return false;
    }
}
