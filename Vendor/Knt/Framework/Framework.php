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

require_once('Config/const.php');

/* Ok, we can continue with the required files inclusion, the uses of the required namespaces, and everything */

require_once('Core/IRequest.php');
require_once('Exception/KntFrameworkException.php');

use Knt\Framework\Core\IRequest;

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

    protected $request = null; //Request object

    /**
     * Constructor. Initialize a new instance of the Framework with the given IRequest object.
     * 
     * @param IRequest $request The request that will be handled by the framework. Default null.
     * If null, the handled request will be initialize with some default values.
     */
    public function __construct(IRequest $request = null) {

        if ($request == null) {

            require_once('Core/Request.php');
            $this->request = new Core\Request();
        
        } else {
        
            $this->request = $request;
        
        }

    }

    /**
     * This static method will handle the given request. It will initialize a new
     * instance of the Framework, find then execute the requested method.
     *
     * @param IRequest $request The request to handle. Default null. If null, will initialize
     * a new default Request object.
     * @return Framework The instancied Framework instance.
     */
    public static function handleRequest(IRequest $request = null) {
        
        $instance = new Framework($request);

        $instance->getView()->render();

        return $instance;

    }

    /**
     *
     */
    public function getView($requestedView = null) {

        $viewPath   = rtrim(BASE_PATH, '\\/') . '/' . trim(VIEWS_PATH, '\\/');
        $viewFile   = null;
        $class      = null;
        $method     = null;

        $viewFile   = $this->_retrieveView($viewPath, $class, $method, $requestedView ?: $this->request->path);

        if ($viewFile !== null) {
            require($viewFile);

            if (is_subclass_of((string)$class, 'Knt\Framework\Core\IView')) {
                $view = new $class;
                $view->setMethod($method)->setQuery($this->request->get);
                return $view;
            }

        }

        //TODO: return 404
        return null;

    }

    /**
     * Retrieve the view file for the given requested path.
     * The requested path basically include view path, name, then method to do.
     * But it may also be only a view path and name (method will be the default one),
     * or only a view path (view name and method name will be the default ones).
     *
     * @param string $viewPath The path where to look for views
     * @param &string &$viewName Will return the name of the desired view
     * @param &string &$methodName Will return the name of the desired method
     * @param string $requestedPath The requested path. 
     * If empty it will look for a default view (default '').
     * @return string The full path of the file containing the desired view
     */
    protected function _retrieveView($viewPath, &$viewName, &$methodName, $requestedPath = '') {

        //The view path should be a valid directory.
        if (!is_dir($viewPath))
            throw new Exception\KntFrameworkException('Please review the framework configuration. The base views path seems to not exists');

        
        $requestedPath      = trim($requestedPath, '/');

        //the requested method should be the last part of the requested path
        $requestedMethod    = trim(substr($requestedPath, strrpos($requestedPath, '/')), '/');

        //We remove the requested method of the requested path
        $requestedPath      = substr($requestedPath, 0, strrpos($requestedPath, '/'));

        //If we have a requested method but no requested path, the requested method is actually the requested path
        if (strlen($requestedPath) == 0 && strlen($requestedMethod) > 0) {
            $requestedPath      = $requestedMethod;
            $requestedMethod    = VIEWS_INDEX;
        }

        //If no requested path, we will look for the default view
        $requestedPath      = $requestedPath    ?: DEFAULT_VIEW;

        //If no requested method,we will use the default method
        $requestedMethod    = $requestedMethod  ?: VIEWS_INDEX;


        $possibleViewFiles = array(
            //The request is well formed
            //Example: /View/method => View.php, Folder/View/method => Folder/View.php
            $viewPath . '/' . $requestedPath . VIEWS_EXTENSION
                => $requestedMethod,

            //The method is actually the view. Method will be the default one
            //Example: /Folder/View => Folder/View.php (method => index)
            $viewPath . '/' . $requestedPath . '/' . $requestedMethod . VIEWS_EXTENSION
                => VIEWS_INDEX,
            
            //The view is actually the folder which contains views. The requested view may be the default one. No method were requested
            //Example: /Folder => Folder/Index.php (method => index)
            $viewPath . '/' . $requestedPath . '/' . DEFAULT_VIEW . VIEWS_EXTENSION
                => $requestedMethod,
            
            //The requested method is actually the folder wich contains the views. The requested view may be the default one.
            //Example: /Folder/View => /Folder/View/Index.php (method => index)
            $viewPath . '/' . $requestedPath . '/' . $requestedMethod . '/' . DEFAULT_VIEW . VIEWS_EXTENSION
                => VIEWS_INDEX
        );

        //The first found will be the good one. Other ones will be ignored.
        foreach ($possibleViewFiles as $possibleViewFile => $possibleMethod) {
            if (is_file($possibleViewFile)) {
                $viewName   = substr($possibleViewFile, strrpos($possibleViewFile, '/') + 1, -strlen(VIEWS_EXTENSION));
                $methodName = $possibleMethod;
                return $possibleViewFile;
            }
        }

        //No view found :s
        return $viewName = $methodName = null;

    }

    /**
     * Return the request object of the current Framework object
     *
     * @return IRequest The request corresponding to the current Framework instance
     */
    public function getRequest() {
        
        return $this->request;
    
    }

}
