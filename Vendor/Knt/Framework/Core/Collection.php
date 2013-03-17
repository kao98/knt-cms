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
 * Collection.php
 * Creation date: 28 nov. 2012
 * 
 * The Collection class aims to be a kind of array object.
 * I must confess that this class has been inspired by the
 * Fabien Potentier's ParameterBag class (https://github.com/symfony/symfony/blob/master/src/Symfony/Component/HttpFoundation/ParameterBag.php).
 *
 * Version 1.0: Initial version
 * 
 * @package Knt\Framework
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
class Collection implements ICollection
{
    
    /**
     * The container that will store the data
     *
     * @var array
     */
    protected $dataContainer;

    /**
     * The constructor initialize the Collection with the given data
     *
     * @param array $data (default: empty array) The data used to initialize our collection
     */
    public function __construct(array $data = array()) {
        $this->dataContainer = $data;
    }

    /**
     * \Countable implementation
     * Return the number of items in the collection
     *
     * @return int The number of items in the colleciton
     */
    public function count() {
        return count($this->dataContainer);
    }

    /**
     * \IteratorAggregate implementation
     * Return an iterator for the collection
     *
     * @return \ArrayIterator An \ArrayIterator instance on the collection
     */
    public function getIterator() {
        return new \ArrayIterator($this->dataContainer);
    }

    /**
     * Return the requested data identified by its index
     *
     * @param $index The index of the desired data
     * @param $default The default value if $index is not found. Default null.
     * @return mixed The data located at $index. $default if the desired data doesn't exist.
     */
    public function get($index, $default = null) {
        return $this->_deepSearch($this->dataContainer, $index, $default);
    }

    /**
     * Search into an array for an index. Perform a deep search (look for arrays into arrays) if the
     * index is a string containing brackets
     *
     * @param array &$array The array to search in
     * @param $index The index to search in the array
     * @param $default The default value to return if the search fail. Default null.
     * @return mixed The desired value. $default if $index can't be found
     * @example 
     *  $array = array(0 => array(0 => array(1 => 1)));
     *  $this->_deepSearch('0');        // return array(0 => array(1 => 1))
     *  $this->_deepSearch('0[0]');     // return array(1 => 1);
     *  $this->_deepSearch('0[0][1]');  // return 1;
     *  $this->_deepSearch('0[1]');     // return null;
     *  $this->_deepSearch('0[1]', 0);  // return 0;
     */
    protected function _deepSearch(array $array, $index, $default = null) {

        $stop = false;

        while(!isset($array[$index]) && !$stop) {

            $stop       = true;
            $posStart   = strpos($index, '[');
            $posEnd     = strpos($index, ']');
            $arrayIndex = null;

            if ($posStart !== false && $posEnd !== false) {

                //Extracting the first array index
                $arrayIndex = substr($index, 0, $posStart);

            }

            if ($arrayIndex !== null && isset($array[$arrayIndex]) && is_array($array[$arrayIndex])) {

                //Building of a new index string without the first level
                $newIndex = trim(substr($index, $posStart + 1, $posEnd - $posStart - 1), "'\"") . substr($index, $posEnd + 1);

                $array = $array[$arrayIndex];
                $index = $newIndex;

                $stop = false;

            } 

        }

        return isset($array[$index]) ? $array[$index] : $default;

    }

    /**
     * Store a new data in the collection or update the data identified by the specified key. 
     * The new data will be identified with the specified key.
     * 
     * @param $key The key of the data to set
     * @param $value The new value to store in the collection
     * @return Collection the current Collection 
     */
    public function set($key, $value = null) {
        $this->dataContainer[$key] = $value;
        return $this;
    }

    /**
     * Add the value to the collection
     * then return the key of the value
     *
     * @param $value The value to add
     * @return int The key of the newly added value
     */
    public function add($value) {
        
        $this->dataContainer[] = $value;
        end($this->dataContainer);
        $key = key($this->dataContainer);
        reset($this->dataContainer);

        return $key;
    }

}
