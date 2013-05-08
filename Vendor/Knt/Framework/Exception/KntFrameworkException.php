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

namespace Knt\Framework\Exception;

/**
 * KntFrameworkException.php
 * 
 * KNT Framework Exception class.
 * Exception thrown by the framework itself. 
 * May be used as base class for all exception thrown by the framework.
 * 
 * Version 1.0: Initial version
 *
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
class KntFrameworkException extends \Exception
{
    public function __construct($message, $code = 500, $previous = null) {
        
        parent::__construct($message, $code, $previous);
        header("HTTP/1.0 $code");
        
    }    
}