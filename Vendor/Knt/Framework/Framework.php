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

        $instance->getController()->handle();

        return $instance;

    }

    /**
     *
     */
    public function getController($requestedController = null) {

        $controllerPath = trim(BASE_PATH, '\\/') . '/' . trim(CONTROLLERS_PATH, '\\/');

        $controllerFile = null;
        $class          = null;
        $action         = null;

        $controllerFile = $this->_retrieveController($controllerPath, $class, $action, $requestedController ?: $this->request->path);
        if ($controllerFile !== null) {
            require($controllerFile);
            return new $class;
        }

        //TODO: return 404
        return null;

    }

    /**
     * Retrieve the controller file for the given requested path.
     * The requested path basically include controller path, name, then action to do.
     * But it may also be only a controller path and name (action will be the default one),
     * or only a controller path (controller name and action name will be the default ones).
     *
     * @param string $controllerPath The path where to look for controllers
     * @param &string &$controllerName Will return the name of the desired controller
     * @param &string &$actionName Will return the name of the desired action
     * @param string $requestedPath The requested path. 
     * If empty it will look for a default controller (default '').
     * @return string The full path of the file containing the desired controller
     */
    protected function _retrieveController($controllerPath, &$controllerName, &$actionName, $requestedPath = '') {

        //The controller path should be a valid directory.
        if (!is_dir($controllerPath))
            throw new Exception\KntFrameworkException('Please review the framework configuration. The base controllers path seems to not exists');

        
        $requestedPath      = trim($requestedPath, '/');

        //the requested action should be the last part of the requested path
        $requestedAction    = trim(substr($requestedPath, strrpos($requestedPath, '/')), '/');

        //We remove the requested action of the requested path
        $requestedPath      = substr($requestedPath, 0, strrpos($requestedPath, '/'));

        //If we have a requested action but no requested path, the requested action is actually the requested path
        if (strlen($requestedPath) == 0 && strlen($requestedAction) > 0) {
            $requestedPath      = $requestedAction;
            $requestedAction    = CONTROLLERS_INDEX;
        }

        //If no requested path, we will look for the default controller
        $requestedPath      = $requestedPath    ?: DEFAULT_CONTROLLER;

        //If no requested action,we will use the default action
        $requestedAction    = $requestedAction  ?: CONTROLLERS_INDEX;


        $possibleControllerFiles = array(
            //The request is well formed
            //Example: /Controller/action => Controller.php, Folder/Controller/action => Folder/Controller.php
            $controllerPath . '/' . $requestedPath . CONTROLLERS_EXTENSION
                => $requestedAction,

            //The action is actually the controller. Action will be the default one
            //Example: /Folder/Controller => Folder/Controller.php (action => index)
            $controllerPath . '/' . $requestedPath . '/' . $requestedAction . CONTROLLERS_EXTENSION
                => CONTROLLERS_INDEX,
            
            //The controller is actually the folder which contains controllers. The requested controller may be the default one. No action were requested
            //Example: /Folder => Folder/Index.php (action => index)
            $controllerPath . '/' . $requestedPath . '/' . DEFAULT_CONTROLLER . CONTROLLERS_EXTENSION
                => $requestedAction,
            
            //The requested action is actually the folder wich contains the controllers. The requested controller may be the default one.
            //Example: /Folder/Controller => /Folder/Controller/Index.php (action => index)
            $controllerPath . '/' . $requestedPath . '/' . $requestedAction . '/' . DEFAULT_CONTROLLER . CONTROLLERS_EXTENSION
                => CONTROLLERS_INDEX
        );

        //The first found will be the good one. Other ones will be ignored.
        foreach ($possibleControllerFiles as $possibleControllerFile => $possibleAction) {
            if (is_file($possibleControllerFile)) {
                $controllerName = substr($possibleControllerFile, strrpos($possibleControllerFile, '/') + 1, -strlen(CONTROLLERS_EXTENSION));
                $actionName     = $possibleAction;
                return $possibleControllerFile;
            }
        }

        //No controller found :s
        return $controllerName = $actionName = null;

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
