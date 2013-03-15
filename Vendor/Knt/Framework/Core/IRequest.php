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

namespace Knt\Framework\Core;

require_once('ICollection.php');

/**
 * Request.php
 * Creation date: 15 dec. 2012
 * 
 * KNT Request class.
 * Represent the GET from a http request.
 * 
 * Version 1.0: Initial version
 *
 * @package Knt\Framework\Core
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
interface IRequest
{
    /**
     * Constructor. Initialize the Request with the given data.
     * Data may be null, then the Request will be initialized with
     * default data.
     * 
     * @param string $path The information regarding path detail of the request (as something like $_SERVER['PATH_INFO']))
     * @param ICollection $get An ICollection containing the get part of the request (as something like $_GET)
     * @param ICollection $post An ICollection containing the post part of the request (as something like $_POST)
     */
    public function __construct($path = null, ICollection $get = null, ICollection $post = null);

}