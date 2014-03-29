<?php

/* 
 * knt-cms: another Content Management System (http://www.kaonet-fr.net/cms)
 * 
 * Licensed under The MIT License
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * @link          http://www.kaonet-fr.net/cms
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace Knt\Framework;

/* Configuration uses constants defined in Config/const.php */

require_once 'Config/const.php';

/* Ok, we can continue with the required files inclusion, the uses of the required namespaces, and everything */

use 
    \Knt\Framework\Core\RequestInterface,
    \Knt\Framework\Core\Routeur
;

/**
 * Framework.php
 * Creation date: 27 nov. 2012
 * 
 * KNT Framework main class.
 * It provide the static HandleRequest method that initialize the
 * framework and handle the client request.
 * 
 * Version 1.0: Initial version
 *
 * @package Knt\Framework
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
class Framework
{
    
    const COMPONENT_TYPE_VIEW       = 'view';
    const COMPONENT_TYPE_CONTROLLER = 'controller';
    
    protected static    $_instance  = null; //Singleton instance
    protected           $_request   = null; //The request instance
    protected           $_routeur   = null; //The routeur associated to the framework

    /**
     * Constructor. Initialize a new instance of the Framework with the given Request object.
     * 
     * @param RequestInterface $request The request that will be handled by the framework. Default null.
     * If null, the handled request will be initialize with some default values.
     */
    private function __construct(Routeur\RouteurInterface $routeur = null, RequestInterface $request = null) {

        $this
            ->setRequest($request)
            ->setRouteur($routeur)
        ;

    }

    /**
     * static class: no clone.
     */
    private function __clone() { }
        
    /**
     * Singleton implementation: return the Framework instance.
     * Initialize / set the request of the Framework instance with the given request object.
     * Initialize / set the routeur of the Framework instance with the given routeur object.
     * 
     * @param RequestInterface $request (default null) A request wich will be passed to the Framework instance.
     * If null, and no instance of a Framework exists, 
     * the instance will be initialized with a new default Request object.
     * @param Routeur\RouterInterface $routeur (default null) A routeur to use with the framework.
     * If null, and no instance of the framework exists,
     * a new instance will be initialized with a new default Routeur object.
     * @return Framework the singleton instance 
     */
    public static function getInstance(Routeur\RouteInterface $routeur = null, RequestInterface $request = null) {
        
        if (self::$_instance !== null) {
            if ($request !== null) {
                self::$_instance->setRequest($request);
            }
            if ($routeur !== null) {
                self::$_instance->setRouteur($routeur);
            }
        }
        
        return self::$_instance 
            ?: self::$_instance = new Framework($routeur, $request);
        
    }
    
    /**
     * This static method will handle the given request. It will initialize a new
     * instance of the Framework, find then execute the requested method.
     *
     * @param RequestInterface $request The request to handle. Default null. If null, will initialize
     * a new default Request object.
     * @return Framework The instancied Framework instance.
     */
    public static function handleRequest(Routeur\RouteurInterface $routeur = null, RequestInterface $request = null) {
        
        if (DEBUG_LEVEL > 0) {
            $startingTime = microtime(true); //TODO: refactor that
        }
        
        $instance = self::getInstance($routeur, $request);
        
        if (RequestInterface::METHOD_GET === $instance->request->getMethod()) {
            
            $instance
                ->loadComponent()
                ->render()
            ;
            
        } else {
            
            $instance
                ->loadComponent(self::COMPONENT_TYPE_CONTROLLER)
                ->call()
            ;
            //TODO: retrieve the view from the controller just called then render it
        }
        
        if (DEBUG_LEVEL > 0) {
            echo '<br /><pre>' . round((microtime(true) - $startingTime) * 1000, 3) . 'ms</pre>';
            //TODO: refactor that
        }
        
    }

    /**
     * Retrieve the URI of the route that lead to a controller
     * that can process the given query.
     * @param string $query the query string that need a controller to be processed
     * @return string the uri of one route that lead to a controller
     * @throws Exception\KntFrameworkException 404 if no route can be find for the given query.
     */
    private function _retrieveControllerRouteUri($query) {
        
        if (!
            $this
                ->getRouteur()
                ->exists($query, CONTROLLERS_PATH, CONTROLLERS_EXTENSION)
            ) {
            
            throw new Exception\KntFrameworkException('Not Found', 404);
            
        }
        
        return $query;
    }
    
    /**
     * Retrieve the URI of the route that can
     * return the desired view identified by $query.
     * @param string $query the query string that ask for a view
     * @return string the uri of one route that lead to a view
     * @throws Exception\KntFrameworkException 404 if no route can be find for the given query.
     */
    private function _retrieveViewRouteUri($query) {
        
        $exists = function($uri) {
            return $this
                ->getRouteur()
                ->exists($uri, VIEWS_PATH, VIEWS_EXTENSION)
            ;
        };
        
        $query  = rtrim($query, '/'); //Required to avoid 'double /' in the uri (think about the 'root' query)
        $uri1   = $query . '/' . VIEWS_INDEX;
        $uri2   = $query . '/' . DEFAULT_VIEW . '/' . VIEWS_INDEX;
        
        if ($exists($uri1)) {
            return $uri1;
        }
   
        if ($exists($uri2)) {
            return $uri2;
        }
        
        //Ok. Surrender :-(
        throw new Exception\KntFrameworkException('Not Found', 404);  
        
    }
    
    /**
     * Retrieve a route that lead to a component
     * that can handle the the given query.
     * This is usefull when used with an 'automated' routeur.
     * @see Routeur\Routeur for more informations about routeurs and 'automated' routeurs
     * @param string $query a query string that ask for a component
     * @param string $componentType the type of the component we are looking for (usefull with 'automated' routeurs)
     * @return string the URI of one route that lead to a component ready to process the query.
     * @throws Exception\KntFrameworkException 404 if no route can be found.
     */
    public function retrieveRouteUri($query, $componentType = self::COMPONENT_TYPE_VIEW) {
        
        $routeur = $this->getRouteur();
        
        //Caution: this basic call is required with an non-automated (static) routeur
        if ($routeur->exists($query)) {
            return $query;
        }

        if (is_subclass_of($routeur, 'Knt\Framework\Core\Routeur\AutomatedRouteurInterface')) {

            if ($componentType !== self::COMPONENT_TYPE_VIEW) {
                return $this->_retrieveControllerRouteUri($query);
            }

            return $this->_retrieveViewRouteUri($query);
              
        }
        
        //Ok. Surrender :-(
        throw new Exception\KntFrameworkException('Not Found', 404);  
    }
    
    /**
     * Load a component given it's type and a query
     * @param string $componentType (default self::COMPONENT_TYPE_VIEW) type of the component.
     * Can be self::COMPONENT_TYPE_VIEW, or self::COMPONENT_TYPE_CONTROLLER.
     * @param string $query (default null) a query resulting in a component.
     * If null, will get the request for the curent framework instance.
     * @return \Knt\Framework\Core\Component\*Interface an instance implementing a component interface
     * @throws Exception\KntFrameworkException 
     * 404 if no route to the component can be found
     * 400 if a the component has been queried in a wrong way, or the query don't match to a valid component.
     */
    public function loadComponent($componentType = self::COMPONENT_TYPE_VIEW, $query = null) {
        
        if (!$query) {
            $query = $this
                ->getRequest()
                ->getQueriedPath()
            ;
        }
        
        $routeUri   = $this->retrieveRouteUri($query, $componentType);
        $route      = $this->getRouteur()->getRoute($routeUri);
        $class      = $this->getProjectNamespace() . strtr($route->getComponentName(), '/', '\\');
        $interface  = 'Knt\Framework\Core\Component\\' . ucfirst($componentType) . 'Interface';

        if (is_subclass_of("$class", $interface)) {
            return new $class($this, $route->getMethodName());
        }
        
        throw new Exception\KntFrameworkException('Bad request.', 400);
    }
    
    /**
     * Return a formated version of the PROJECT_NAMESPACE ready to be used.
     * These method ensure that PROJECT_NAMESPACE starts and ends with one and
     * only one back-slash.
     * @return string
     */
    protected function getProjectNamespace() {
        return sprintf("\\%s\\", trim(PROJECT_NAMESPACE, '\\/'));
    }
    
    /**
     * Return the routeur of the current Framework object
     *
     * @return Routeur\RouteurInterface The routeur corresponding to the current Framework instance
     */
    public function getRouteur() {
        
        return $this->_routeur;
    
    }

    /**
     * Set the routeur for the current Framework instance
     * 
     * @param Routeur\RouteurInterface $routeur (default null) the routeur object. If null, will initialize a default routeur. 
     */
    public function setRouteur(Routeur\RouteurInterface $routeur = null) {
        
        if ($routeur == null) {

            $this->_routeur = new Routeur\AutomatedRouteur;
        
        } else {
        
            $this->_routeur = $routeur;
        
        }
        
        return $this;
        
    }
    
    /**
     * Return the request object of the current Framework object
     *
     * @return RequestInterface The request corresponding to the current Framework instance
     */
    public function getRequest() {
        
        return $this->_request;
    
    }

    /**
     * Set the request for the current Framework instance
     * 
     * @param RequestInterface $request (default null) the request object. If null, will initialize a default request. 
     */
    public function setRequest(RequestInterface $request = null) {
        
        if ($request == null) {

            $this->_request = new Core\Request();
        
        } else {
        
            $this->_request = $request;
        
        }
        
        return $this;
        
    }
    
    /**
     * The magic __get method allow to ask for some properties in usual names
     * like the GET and POST data which are stored in the Request object.
     * 
     * @param string $variableName the name of the desired property
     * @return mixed the requested property 
     */
    public function __get($variableName) {
        
        switch ($variableName) {
            case 'queriedData':
            case 'query':
            case 'get':
                return $this->getRequest()->getQueriedData();
            
            case 'postedData':
            case 'data':
            case 'post':
                return $this->getRequest()->getPostedData();
            
            case 'queriedPath':
            case 'path':
                return $this->getRequest()->getQueriedPath();
    
            case 'request':
                return $this->getRequest();
            
        }
        
    }
    
}
