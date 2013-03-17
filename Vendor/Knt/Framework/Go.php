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

require('Vendor/Knt/Framework/Framework.php');

use \Knt\Framework\Framework;


/**
 * Go.php
 * Creation date: 27 nov. 2012
 * 
 * KNT Framework launcher (bootstrap).
 * 
 * Version 1.0: Initial version
 * 
 * @package Knt\Framework
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
class Knt
{
    public static function Go() {

        Framework::handleRequest();

    }
}