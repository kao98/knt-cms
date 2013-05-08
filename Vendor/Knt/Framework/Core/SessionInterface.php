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
 * SessionInterface.php
 * 
 * KNT Framework session interface.
 * This interface is an Object Oriented interface for session managment.
 * 
 * Version 1.0: Initial version
 *
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
interface SessionInterface 
{

    /**
     * Constructor. Initialize the session.
     * 
     * @param string $name (default null) If specified defines the name of the session
     * @param boolean $autoStart (default true) If true the session will be started automatically on first use
     */
    public function __construct($name = null, $autoStart = true);
    
    /**
     * Use to start the session manager
     */
    public function start();
    
    /**
     * Set / update the name of the session
     * @param string $newName the new name for the session
     */
    public function setName($newName);
    
    /**
     * Return the name of the session
     * @return string the name of the session
     */
    public function getName();
    
    /**
     * Return the requested data stored in the session identified by its index
     *
     * @param string $index The index of the desired data
     * @param mixed $default (default null) The default value if $index is not found.
     * @return mixed The data located at $index. $default if the desired data doesn't exist.
     */
    public function get($index, $default = null);
    
    /**
     * Store a new data in the session or update the data identified by the specified key. 
     * The new data will be identified with the specified key.
     * 
     * @param $key The key of the data to set
     * @param $value (default null) The new value to store in the collection
     * @return Collection the current Collection 
     */
    public function set($key, $value = null);
    
    /**
     * Add the value to the session
     * then return the key of the value
     *
     * @param $value The value to add
     * @return int The key of the newly added value
     */
    public function add($value);
}
