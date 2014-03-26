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
 * Request.php
 * 
 * KNT Request class.
 * Represent a request to handle.
 * It contains information about the Path to deserve, the requested method,
 * and collections containing GET et POST variables.
 * 
 * Version 1.0: Initial version
 *
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
class Request implements RequestInterface
{
    //The folowing array will help during verifying the validity of a method (see setMethod)
    private static $_availableMethods = array(
        self::METHOD_GET, 
        self::METHOD_POST, 
        self::METHOD_PUT, 
        self::METHOD_DELETE);
    
    protected $_queriedPath = null;     //Path to deserve
    protected $_queriedData = null;     //Get variables Collection
    protected $_postedData  = null;     //Posted data Collection
    protected $_method      = null;     //HTTP Method of the request
    
    /**
     * Constructor. Initialize the Request with the given data.
     * Data may be null, then the Request will be initialized with
     * default data from global vars.
     * 
     * @param string $queriedPath 
     * The information regarding path detail of the request (default will use $_SERVER['PATH_INFO']))
     * @param CollectionInterface $queriedData 
     * A Collection containing the get part of the request (default will use $_GET)
     * @param CollectionInterface $postedData 
     * A Collection containing the post part of the request (default will use $_POST)
     */
    public function __construct(
            $queriedPath                        = null, 
            $method                             = null, 
            CollectionInterface $queriedData    = null, 
            CollectionInterface $postedData     = null) {

        $this
            ->setQueriedPath($queriedPath)
            ->setMethod($method)
            ->setQueriedData($queriedData)
            ->setPostedData ($postedData );
        
    }

    /**
     * Return the queried path 
     * 
     * @return string the requested path 
     */
    public function getQueriedPath() {
        return $this->_queriedPath;
    }
    
    /**
     * Set the path info about the request. If no value is specified
     * it will look for default path information
     * 
     * @param string $queriedPath (default null) the new value for the path 
     * @return Request Return the current instance
     */
    public function setQueriedPath($queriedPath = null) {
        
        $this->_queriedPath = $queriedPath ?: $this->_retrieveQueriedPath();
        return $this;
                
    }
    
    /**
     * Return the default path informations as a string
     *
     * @return string The default path found for the current HTTP request
     */
    protected function _retrieveQueriedPath() {

        return isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

    }
    
    /**
     * Return the method for the query (GET, POST, ...)
     * 
     * @return string the method of the query
     */
    public function getMethod() {
        return $this->_method;
    }
    
    /**
     * Set the method for the query. If no value is specified
     * it will look for a default method
     * 
     * @param string $method (default null) the new value for the method 
     * @return Request Return the current instance
     */
    public function setMethod($method = null) {
        
        $method = strtolower($method ?: $this->_retrieveMethod());
        
        if (in_array($method, self::$_availableMethods)) {
            $this->_method = $method;
            return $this;
        }
        
        throw new \Knt\Framework\Exception\KntFrameworkException('Bad method specified');
    }
    
    /**
     * Return the default method information as a string
     *
     * @return string The default method found for the current HTTP request
     */
    protected function _retrieveMethod() {

        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get';

    }
    
    /**
     * Return the collection of GET variables
     * 
     * @return CollectionInterface The GET variables collection 
     */
    public function getQueriedData() {
        
        return $this->_queriedData;
        
    }
    
    /**
     * Set the get info about the request. If no value is specified
     * it will look for default get information as we should find them
     * in the superglobal variables.
     * 
     * @param CollectionInterface $queriedData (default null) the new value for the Collection containing queried data
     * @return Request Return the current instance
     */
    public function setQueriedData(CollectionInterface $queriedData = null) {
        
        $this->_queriedData = $queriedData ?: $this->_retrieveQueriedData();
        return $this;
                
    }
    
    /**
     * Return the default get variables as a Collection object
     *
     * @return Collection A new collection initialized with the get data from the current HTTP request
     */
    protected function _retrieveQueriedData() {

        return new Collection($_GET);

    }

    /**
     * Return the collection of POST variables
     * 
     * @return CollectionInterface The POST variables collection 
     */
    public function getPostedData() {
        
        return $this->_postedData;
        
    }
    
    /**
     * Set the post info about the request. If no value is specified
     * it will look for default post information as we should find them
     * in the superglobal variables.
     * 
     * @param CollectionInterface $postedData (default null) the new value for the Collection containing posted data
     * @return Request Return the current instance
     */
    public function setPostedData(CollectionInterface $postedData = null) {
        
        $this->_postedData = $postedData ?: $this->_retrievePostedData();
        return $this;
                
    }
    
    /**
     * Return the default posted variables as a Collection object
     *
     * @return Collection A new collection initialized with the post data from the current HTTP request
     */
    protected function _retrievePostedData() {

        return new Collection($_POST);

    }

    /**
     * Serializable implementation: serialize the Request class
     * @return string the serialized reprensentation of the Request class 
     */
    public function serialize() {
        return serialize(
                array(
                    'path'      => $this->_queriedPath,
                    'method'    => $this->_method,
                    'query'     => $this->_queriedData,
                    'data'      => $this->_postedData
                )
            );
    }
    
    /**
     * Serializable implementation: unserialize the Request class
     * @param string $data the data that represent the serialized Request class 
     */
    public function unserialize($data) {
        $unserialized = unserialize($data);
        $this->_queriedPath = $unserialized['path'];
        $this->_method      = $unserialized['method'];
        $this->_queriedData = $unserialized['query'];
        $this->_postedData  = $unserialized['data'];
    }
    
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
    public function getAsQueryString() {
        
        $queryString = $this->getQueriedPath();
        
        $queryString .= '?';
        
        foreach ($this->_queriedData as $dataName => $dataValue) {
            $queryString .= "$dataName=$dataValue&";
        }
        
        return rtrim($queryString, '&');
    }
    
    /**
     * Alias of the Request::getAsQueryString() method
     * @return string a query string representation of the actual request 
     */
    public function getQuery() {
        return $this->getAsQueryString();
    }
    
}