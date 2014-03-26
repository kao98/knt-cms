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

/*
 * Constants definition. Those constants may help to customize / configure the framework,
 * mainly the way to define the various path where to find the components (views, ...) 
 */

/**
 * The base path to retrieve the root path of the application, the path that contains
 * the Vendor folder that contains the Knt folder that contains the Framework folder.
 */
DEFINED('BASE_PATH')        OR DEFINE('BASE_PATH',          __DIR__ . '/../../../..');

/**
 * The path to the framework
 */
DEFINED('FRAMEWORK_PATH')   OR DEFINE('FRAMEWORK_PATH',     __DIR__ . '/..');

/**
 * Base namespace of the project
 */
DEFINED('PROJECT_NAMESPACE') OR DEFINE('PROJECT_NAMESPACE', '\Knt\Framework\Sample');

/**
 * The path where to find the views.
 * By default, will be the folder of the sample project provided with the framework.
 * This constant is the only one that really need to be customized.
 */
DEFINED('VIEWS_PATH')       OR DEFINE('VIEWS_PATH',         BASE_PATH . '/Vendor/Knt/Framework/Sample');

/**
 * The name of the default method to call, typically index
 */
DEFINED('VIEWS_INDEX')      OR DEFINE('VIEWS_INDEX',        'index');

/**
 * The default view, typically "Home" or "Index".
 */
DEFINED('DEFAULT_VIEW')     OR DEFINE('DEFAULT_VIEW',       'Home');

/**
 * Views file extension
 */
DEFINED('VIEWS_EXTENSION')  OR DEFINE('VIEWS_EXTENSION',    '.php');

/**
 * The path where to find the controllers.
 * By default, will be the folder of the sample project provided with the framework.
 * This constant is the only one that really need to be customized.
 */
DEFINED('CONTROLLERS_PATH') OR DEFINE('CONTROLLERS_PATH',   BASE_PATH . '/Vendor/Knt/Framework/Sample');

/**
 * Controllers file extension
 */
DEFINED('CONTROLLERS_EXTENSION') OR DEFINE('CONTROLLERS_EXTENSION', '.php');

/**
 * Debug level
 */
DEFINED('DEBUG_LEVEL')      OR DEFINE('DEBUG_LEVEL',        1);