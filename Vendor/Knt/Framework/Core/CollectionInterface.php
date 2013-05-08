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
 * CollectionInterface.php
 * 
 * This interface provides the interface for Collection objects.
 * See the Collection corresponding base implementation (Collection.php) for more details.
 * Version 1.0: Initial version
 * 
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
interface CollectionInterface extends \IteratorAggregate, \Countable , \Serializable
{

    /**
     * The constructor initialize the Collection with the given data
     *
     * @param array $data (default: empty array) The data used to initialize our collection
     * @param bool $useReference (default: false) Pass true to use the $data array by reference instead of by value
     */
    public function __construct(array &$data = array(), $useReference = false);

    /**
     * Return the requested data identified by its index
     *
     * @param $index The index of the desired data
     * @param $default The default value if $index is not found. Default null.
     * @return mixed The data located at $index. $default if the desired data doesn't exist.
     */
    public function get($index, $default = null);

    /**
     * Store a new data in the collection or update the data identified by the specified key. 
     * The new data will be identified with the specified key.
     * 
     * @param $key The key of the data to set
     * @param $value The new value to store in the collection
     * @return Collection the current Collection 
     */
    public function set($key, $value = null);

    /**
     * Add the value to the collection
     * then return the key of the value
     *
     * @param $value The value to add
     * @return int The key of the newly added value
     */
    public function add($value);

}