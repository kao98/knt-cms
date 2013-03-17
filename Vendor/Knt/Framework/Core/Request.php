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

require_once('IRequest.php');
require_once('ICollection.php');

/**
 * Request.php
 * Creation date: 15 dec. 2012
 * 
 * KNT Request class.
 * Represent a request to handle.
 * 
 * Version 1.0: Initial version
 *
 * @package Knt\Framework\Core
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
class Request implements IRequest
{
    public    $path = null;     //Path to deserve
    public    $get  = null;     //Get variables Collection
    protected $post = null;     //Posted data Collection

    /**
     * Constructor. Initialize the Request with the given data.
     * Data may be null, then the Request will be initialized with
     * default data from global vars.
     * 
     * @param string $path The information regarding path detail of the request (default will use $_SERVER['PATH_INFO']))
     * @param ICollection $get An ICollection containing the get part of the request (default will use $_GET)
     * @param ICollection $post An ICollection containing the post part of the request (default will use $_POST)
     */
    public function __construct($path = null, ICollection $get = null, ICollection $post = null) {

        $this->_initialize(
            $path ?: $this->_getDefaultPath(), 
            $get  ?: $this->_getDefaultGet(), 
            $post ?: $this->_getDefaultPost());

    }

    /**
     * Initialize the Request object
     *
     * @param string $path The information regarding path detail of the request
     * @param ICollection $get An ICollection containing the get part of the request
     * @param ICollection $post An ICollection containing the post part of the request
     */
    protected function _initialize($path, ICollection $get, ICollection $post) {

        $this->path = $path;
        $this->get  = $get;
        $this->post = $post;

    }

    /**
     * Return the default path informations as a string
     *
     * @return string The default path found for the current HTTP request
     */
    protected function _getDefaultPath() {

        return isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

    }

    /**
     * Return the default get variables as a Collection object
     *
     * @return Collection A new collection initialized with the get data from the current HTTP request
     */
    protected function _getDefaultGet() {

        require_once('Collection.php');
        return new Collection($_GET);

    }

    /**
     * Return the default posted variables as a Collection object
     *
     * @return Collection A new collection initialized with the post data from the current HTTP request
     */
    protected function _getDefaultPost() {

        require_once('Collection.php');
        return new Collection($_POST);

    }


}