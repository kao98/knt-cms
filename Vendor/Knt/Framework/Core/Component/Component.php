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

namespace Knt\Framework\Core\Component;
use \Knt\Framework\Framework;
use \Knt\Framework\Exception;
use \Knt\Framework\Core\CollectionInterface;

/**
 * Component.php
 * 
 * KNT Component class.
 * Base class for some components as Views or Controllers
 * 
 * Version 1.0: Initial version
 *
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
class Component implements ComponentInterface
{

    private $_framework = null; //an instance of the framework
    private $_method    = null; //the method of the component to be called
    private $_data      = null; //some data passed to the component

    /**
     * Constructor. Initialize the component.
     * 
     * @param Framework\Framework $frameworkInstance an instance of the framework
     * @param string $method the name of the method of the component to call
     * @param CollectionInterface $data a collection of data to pass to the component.
     * Those data will be bind to the method arguments
     * @return Component the current Component instance 
     */
    public function __construct(Framework $frameworkInstance, $method, CollectionInterface $data) {
        
        $this->_framework = $frameworkInstance;
        
        $this->setData   ($data)
             ->setMethod ($method);
        
    }
    
    
    /**
     * Retrieve the desired component file.
     * The requested component is specified in the form of a string
     * that includes the path and the name of the component
     * and the name of te method to call.
     *
     * @param string $request The requested component path. 
     * @param string $path The path where to look for the component
     * @param &string &$componentName Will return the name of the component
     * @param &string &$methodName Will return the name of the method to call
     * @return string The full path of the file containing the desired component
     */
    public static function retrieve(
            $request, 
            $path, 
            &$componentName, 
            &$methodName, 
            $componentExtension = '.php', 
            $defaultComponent   = null, 
            $defaultMethod      = null) {

        $path = self::_prepareComponentPath($path);
        
        $method = '';
        self::_prepareRequestAndMethod($request, $method, $defaultComponent, $defaultMethod);

        $possibleComponentFiles = self::_preparePossibleComponentFiles(
                $path,
                $request,
                $componentExtension,
                $method,
                $defaultMethod,
                $defaultComponent);

        //The first found will be the good one. Other ones will be ignored.
        foreach ($possibleComponentFiles as $possibleComponentFile => $possibleMethod) {
            if (is_file($possibleComponentFile)) {
                
                $componentName  = substr(
                        $possibleComponentFile, strrpos($possibleComponentFile, '/') + 1,
                        -strlen($componentExtension)
                        );
                
                $methodName     = $possibleMethod;
                
                return $possibleComponentFile;
            }
        }

        //No component found :s
        return $componentName = $methodName = null;

    }
    
    /**
     * Prepare the path for the retrieve static method.
     * If the given path doesn't exists, it will throw an exception
     * @param string $path the path to prepare
     * @return string
     * @throws Exception\KntFrameworkException
     */
    private static function _prepareComponentPath($path) {
        $path = rtrim($path, '\\/');        
        if (!is_dir($path)) {
            throw new Exception\KntFrameworkException('The specified path doesn\'t exists');
        }
        return $path;
    }
    
    /**
     * Prepare the request and method for the retrieve static method.
     * Will throw exceptions if no request or method can be computed.
     * @param string $request the original request will be overriden with the real request part of the given string
     * @param string $method the method of the component to call
     * @param string $defaultComponent if no component can be found in the request, will use the default one
     * @param string $defaultMethod if no method can be found in the request, will use the default one
     * @throws Exception\KntFrameworkException
     */
    private static function _prepareRequestAndMethod(&$request, &$method, $defaultComponent, $defaultMethod) {
        $request    = trim($request, '/');
        $method     = trim(substr($request, strrpos($request, '/')), '/');
        $request    = substr($request, 0, strrpos($request, '/'));

        if (strlen($request) == 0 && strlen($method) > 0) {
            $request    = $method;
            $method     = $defaultMethod;
        }

        if (!$request && $defaultComponent === null) {
            throw new Exception\KntFrameworkException('No component requested');
        }
        $request    = $request  ?: $defaultComponent;
        
        if (!$method && $defaultMethod === null) {
            throw new Exception\KntFrameworkException('No method requested');
        }
        $method     = $method   ?: $defaultMethod;
    }
    
    /**
     * compute an array containing various possible file names for the given
     * component parameters
     * @param string $path the path hosting the requested components
     * @param string $request the request asking for a component
     * @param string $componentExtension extension of the components
     * @param string $method the method name of the component to call
     * @param string $defaultMethod the default method
     * @param string $defaultComponent the default component
     * @return array
     */
    private static function _preparePossibleComponentFiles($path, $request, $componentExtension, $method, $defaultMethod, $defaultComponent) {
        $possibleComponentFiles = array();
        $possibleComponentFiles[$path . '/' . $request . $componentExtension] = $method;
        
        if ($defaultMethod !== null) {
            $possibleComponentFiles[$path . '/' . $request . '/' . $method . $componentExtension] = $defaultMethod;
        }
        
        if ($defaultComponent !== null) {
            $possibleComponentFiles[$path . '/' . $request . '/' . $defaultComponent . $componentExtension] = $method;
        }
        
        if ($defaultMethod !== null && $defaultComponent !== null) {
            $possibleComponentFiles
                    [$path . '/' . $request . '/' . $method . '/' . $defaultComponent . $componentExtension]
                    = $defaultMethod;
        }
        return $possibleComponentFiles;
    }
    
    /**
     * Invoke the specified method of the current component.
     * If the method as some arguments, we will try to bind them with the component datas.
     * 
     * @param string $method the method of the component to invoke
     */
    public function invoke($method) {

        if (!method_exists($this, $method)) {
            throw new Exception\KntFrameworkException(
                    sprintf("Component '%s' has no method '%s'", get_class($this), $method)
                    );
        }
        
        $reflection = new \ReflectionMethod($this, $method);

        if (!$reflection->isPublic() || $reflection->isAbstract()) {
            throw new Exception\KntFrameworkException(
                    sprintf("You are not authorized to call %s::%s", get_class($this), $method)
                    );
        }

        $args       = array();
        $parameters = $reflection->getParameters();

        foreach ($parameters as $parameter) {

            if ($parameter->canBePassedByValue()) {

                $arg = $this->getData()->get($parameter->getName(), null);
                if ($arg === null && $parameter->isDefaultValueAvailable()) {
                    $arg = $parameter->getDefaultValue();
                }
                
                $args[] = $arg;

            } else {
                throw new Exception\KntFrameworkException("{$parameter->getName()} cannot be passed by reference");
            }


        }

        $reflection->invokeArgs($this, $args);

    }

    public function __invoke($method = null) {
        
        if ($method === null && $this->getMethod() === null) {
            throw new Exception\KntFrameworkException('Method to invoke is missing.');
        }
        
        $this->invoke($method ?: $this->getMethod());
        
    }
    
    /**
     * Set the method of the component to be invoke
     * @param string $method The name of the method to call
     * @return \Knt\Framework\Core\Component\Component The current instance of the component
     */
    public function setMethod($method) {

        $this->_method = $method;
        return $this;

    }

    /**
     * Return the name of the method of the component to be invoke
     * @return string the name of the method to be called
     */
    public function getMethod() {

        return $this->_method;

    }

    /**
     * Set the data associated to the component
     * @param \Knt\Framework\Core\CollectionInterface $data the data to associate with the component
     * @return \Knt\Framework\Core\Component\Component the current instance of the componnent
     */
    public function setData(CollectionInterface $data) {
        
        $this->_data = $data;
        return $this;

    }

    /**
     * Return the data associated to the component
     * @return \Knt\Framework\Core\CollectionInterface the data associated to the component
     */
    public function getData() {

        return $this->_data;

    }
    
 }
