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

use \Knt\Framework\Core\RequestInterface;

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
    protected static    $_instance  = null; //Singleton instance
    protected           $_request   = null; //Request object

    /**
     * Constructor. Initialize a new instance of the Framework with the given Request object.
     * 
     * @param RequestInterface $request The request that will be handled by the framework. Default null.
     * If null, the handled request will be initialize with some default values.
     */
    private function __construct(RequestInterface $request = null) {

        $this->setRequest($request);

    }

    /**
     * static class: no clone.
     */
    private function __clone() { }
        
    /**
     * Singleton implementation: return the Framework instance.
     * Initialize / set the request of the Framework instance with the given request object.
     * 
     * @param RequestInterface $request (default null) A request wich will be passed to the Framework instance.
     * If null, and no instance of a Framework exists, 
     * the instance will be initialized with a new default Request object.
     * @return Framework the singleton instance 
     */
    public static function getInstance(RequestInterface $request = null) {
        
        if (self::$_instance !== null && $request !== null) {
            self::$_instance->setRequest($request);
        }
        
        return self::$_instance ?: self::$_instance = new Framework($request);        
        
    }
    
    /**
     * This static method will handle the given request. It will initialize a new
     * instance of the Framework, find then execute the requested method.
     *
     * @param RequestInterface $request The request to handle. Default null. If null, will initialize
     * a new default Request object.
     * @return Framework The instancied Framework instance.
     */
    public static function handleRequest(RequestInterface $request = null) {
        
        if (DEBUG_LEVEL > 0) {
            $startingTime = microtime(true); //TODO: refactor that
        }
        
        $instance   = self::getInstance($request);
        
        if ($instance->getRequest()->getMethod() !== RequestInterface::METHOD_GET) {
            $instance->getComponent('Controller')->call();
            //TODO: retrieve the view from the controller just called then render it
        } else {
            $instance->getComponent('View')->render();
        }
        
        if (DEBUG_LEVEL > 0) {
            echo '<br /><pre>' . round((microtime(true) - $startingTime) * 1000, 3) . 'ms</pre>';
            //TODO: refactor that
        }
        
    }

    /**
     * Return the instance of the requested component.
     * The component is initialized, ready to go.
     * 
     * @param string $componentType the type of the component ('View' or 'Controller')
     * @param string $requestedComponent (default null) 
     * the requested path that should lead to the component.
     * If null, will try to use the queried path
     * @return mixed the instance of the requested component.
     * @exception todo
     */
    public function getComponent($componentType, $requestedComponent = null) {

        $class      = null;
        $method     = null;
        $componentType = ucfirst(strtolower($componentType));
        
        switch ($componentType) {
            case 'View':
                $componentFile = 
                    Core\Component\Component::retrieve(
                            $requestedComponent ?: $this->queriedPath, 
                            VIEWS_PATH, 
                            $class, 
                            $method, 
                            VIEWS_EXTENSION, 
                            DEFAULT_VIEW, 
                            VIEWS_INDEX
                            );
                break;
            case 'Controller':
                $componentFile = 
                    Core\Component\Component::retrieve(
                            $requestedComponent ?: $this->queriedPath, 
                            CONTROLLERS_PATH, 
                            $class, 
                            $method, 
                            CONTROLLERS_EXTENSION
                            );
                break;
            default:
                throw new Exception\KntFrameworkException('Unrecognized component type');
        }
        
        if ($componentFile !== null) {
            
            include_once $componentFile;
            $class = $this->getProjectNamespace() . $class;
            if (is_subclass_of("$class", 'Knt\Framework\Core\Component\\' . $componentType . 'Interface')) {
                
                $component = new $class($this, $method);
                
                return $component;
                
            } else {
                throw new Exception\KntFrameworkException('Bad request.', 400);
            }

        } else {
            throw new Exception\KntFrameworkException('Requested component not found.', 404);
        }

    }
    
    protected function getProjectNamespace() {
        return sprintf("\\%s\\", trim(PROJECT_NAMESPACE, '\\/'));
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
