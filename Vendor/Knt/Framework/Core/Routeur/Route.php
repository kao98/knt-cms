<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Knt\Framework\Core\Routeur;

/**
 * Description of Route
 *
 * @author Aurelien
 */
class Route implements RouteInterface {
    //put your code here
    
    private $_uri;
    private $_componentName;
    private $_methodName;
    
    
    public function __construct($uri, $componentName, $methodName) {
        $this->_uri = $uri;
        $this->_componentName = $componentName;
        $this->_methodName = $methodName;
    }
    
    public function getUri() {
        return $this->_uri;
    }
    
    public function getComponentName() {
        return $this->_componentName;
    }
    
    public function getMethodName() {
        return $this->_methodName;
    }
}
