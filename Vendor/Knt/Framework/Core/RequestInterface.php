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

/**
 * RequestInterface.php
 * 
 * KNT Request interface.
 * Represent a request.
 * 
 * Version 1.0: Initial version
 *
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
interface RequestInterface extends \Serializable
{
    /**
     * Constructor. Initialize the Request with the given data.
     * Data may be null, then the Request will be initialized with
     * default data.
     * 
     * @param string $path The information regarding path detail of the request (as something like $_SERVER['PATH_INFO']))
     * @param CollectionInterface $get A Collection containing the get part of the request (as something like $_GET)
     * @param CollectionInterface $post A Collection containing the post part of the request (as something like $_POST)
     */
    public function __construct($queriedPath = null, $method = null, CollectionInterface $queriedData = null, CollectionInterface $postedData = null);

    /**
     * Return the queried path 
     * 
     * @return string the requested path 
     */
    public function getQueriedPath();
    
    /**
     * Set the path info about the request. If no value is specified
     * it will look for default path information
     * 
     * @param string $queriedPath (default null) the new value for the path 
     * @return Request Return the current instance
     */
    public function setQueriedPath($queriedPath = null);
    
    /**
     * Return the method for the query (GET, POST, ...)
     * 
     * @return string the method of the query
     */
    public function getMethod();
    
    /**
     * Set the method for the query. If no value is specified
     * it will look for a default method
     * 
     * @param string $method (default null) the new value for the method 
     * @return Request Return the current instance
     */
    public function setMethod($method = null);
    
    /**
     * Return the collection of GET variables
     * 
     * @return CollectionInterface The GET variables collection 
     */
    public function getQueriedData();
    
    /**
     * Set the get info about the request. If no value is specified
     * it will look for default get information as we should find them
     * in the superglobal variables.
     * 
     * @param CollectionInterface $queriedData (default null) the new value for the Collection containing queried data
     * @return Request Return the current instance
     */
    public function setQueriedData(CollectionInterface $queriedData = null);
    
    /**
     * Return the collection of POST variables
     * 
     * @return CollectionInterface The POST variables collection 
     */
    public function getPostedData();
    
    /**
     * Set the post info about the request. If no value is specified
     * it will look for default post information as we should find them
     * in the superglobal variables.
     * 
     * @param CollectionInterface $postedData (default null) the new value for the Collection containing posted data
     * @return Request Return the current instance
     */
    public function setPostedData(CollectionInterface $postedData = null);
    
    /**
     * Return a representation of the current request as an query string.
     * Caution: this method don't care the posted data!!! The returned
     * string contains the path and the queried data, that's all.
     * 
     * @return string a query string representation of the actual request 
     * @example 
     * $req = new Request('/test', new Collection(new Array('arg1' => 'foo', 'arg2' => 'bar'));
     * print($req->getAsUriString()); //Result will be "/test?arg1=foo&arg2=bar"
     */
    public function getAsQueryString();
    
    /**
     * Alias of the Request::getAsQueryString() method
     * @return string a query string representation of the actual request 
     */
    public function getQuery();
     
}