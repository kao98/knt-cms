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
    protected $action  = null; //The action to handle

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
     * instance of the Framework, find then execute the requested action.
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

        $viewPath   = trim(BASE_PATH, '\\/') . '/' . trim(VIEWS_PATH, '\\/');
        $viewFile   = null;
        $class      = null;
        $action     = null;

        $viewFile   = $this->_retrieveView($viewPath, $class, $action, $requestedView ?: $this->request->path);

        if ($viewFile !== null) {
            require($viewFile);
            return new $class;
        }

        //TODO: return 404
        return null;

    }

    /**
     * Retrieve the view file for the given requested path.
     * The requested path basically include view path, name, then action to do.
     * But it may also be only a view path and name (action will be the default one),
     * or only a view path (view name and action name will be the default ones).
     *
     * @param string $viewPath The path where to look for views
     * @param &string &$viewName Will return the name of the desired view
     * @param &string &$actionName Will return the name of the desired action
     * @param string $requestedPath The requested path. 
     * If empty it will look for a default view (default '').
     * @return string The full path of the file containing the desired view
     */
    protected function _retrieveView($viewPath, &$viewName, &$actionName, $requestedPath = '') {

        //The view path should be a valid directory.
        if (!is_dir($viewPath))
            throw new Exception\KntFrameworkException('Please review the framework configuration. The base views path seems to not exists');

        
        $requestedPath      = trim($requestedPath, '/');

        //the requested action should be the last part of the requested path
        $requestedAction    = trim(substr($requestedPath, strrpos($requestedPath, '/')), '/');

        //We remove the requested action of the requested path
        $requestedPath      = substr($requestedPath, 0, strrpos($requestedPath, '/'));

        //If we have a requested action but no requested path, the requested action is actually the requested path
        if (strlen($requestedPath) == 0 && strlen($requestedAction) > 0) {
            $requestedPath      = $requestedAction;
            $requestedAction    = VIEWS_INDEX;
        }

        //If no requested path, we will look for the default view
        $requestedPath      = $requestedPath    ?: DEFAULT_VIEW;

        //If no requested action,we will use the default action
        $requestedAction    = $requestedAction  ?: VIEWS_INDEX;


        $possibleViewFiles = array(
            //The request is well formed
            //Example: /View/action => View.php, Folder/View/action => Folder/View.php
            $viewPath . '/' . $requestedPath . VIEWS_EXTENSION
                => $requestedAction,

            //The action is actually the view. Action will be the default one
            //Example: /Folder/View => Folder/View.php (action => index)
            $viewPath . '/' . $requestedPath . '/' . $requestedAction . VIEWS_EXTENSION
                => VIEWS_INDEX,
            
            //The view is actually the folder which contains views. The requested view may be the default one. No action were requested
            //Example: /Folder => Folder/Index.php (action => index)
            $viewPath . '/' . $requestedPath . '/' . DEFAULT_VIEW . VIEWS_EXTENSION
                => $requestedAction,
            
            //The requested action is actually the folder wich contains the views. The requested view may be the default one.
            //Example: /Folder/View => /Folder/View/Index.php (action => index)
            $viewPath . '/' . $requestedPath . '/' . $requestedAction . '/' . DEFAULT_VIEW . VIEWS_EXTENSION
                => VIEWS_INDEX
        );

        //The first found will be the good one. Other ones will be ignored.
        foreach ($possibleViewFiles as $possibleViewFile => $possibleAction) {
            if (is_file($possibleViewFile)) {
                $viewName   = substr($possibleViewFile, strrpos($possibleViewFile, '/') + 1, -strlen(VIEWS_EXTENSION));
                $actionName = $possibleAction;
                return $possibleViewFile;
            }
        }

        //No view found :s
        return $viewName = $actionName = null;

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
