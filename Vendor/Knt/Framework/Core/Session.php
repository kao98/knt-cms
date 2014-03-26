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
use \Knt\Framework\Core\Collection;
use \Knt\Framework\Exception;

/**
 * Session.php
 * 
 * KNT Framework session class.
 * This class is just a Object Oriented implementation of the php native sessions.
 * 
 * Version 1.0: Initial version
 *
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
class Session 
{
    private   $_collection  = null; //the collection used for the session variables
    protected $_started     = false;//indicates if the session has already ben started or not
    protected $_autoStart   = true; //must we automatically start the session manager on first use?
    
    /**
     * Constructor. Initialize the session.
     * 
     * @param string $name (default null) If specified defines the name of the session
     * @param boolean $autoStart (default true) If true the session will be started automatically on first use
     */
    public function __construct($name = null, $autoStart = true) {                
        $this->_autoStart = $autoStart;
        if ($name !== null) {
            $this->setName($name);
        }
    }
    
    /**
     * Use to start the session manager
     */
    public function start() {
        if ($this->_started) {
            return $this;
        }
        
        session_start();
        
        $this->_collection = new Collection($_SESSION, true);
        
        $this->_started = true;
        return $this;
    }
    
    /**
     * Set / update the name of the session
     * @param string $newName the new name for the session
     */
    public function setName($newName) {        
        session_name($newName);
        
        return $this;
    }
    
    /**
     * Return the name of the session
     * @return string the name of the session
     */
    public function getName() {
        return session_name();
    }
    
    /**
     * Return the requested data stored in the session identified by its index
     *
     * @param string $key The index of the desired data
     * @param mixed $default (default null) The default value if $index is not found.
     * @return mixed The data located at $index. $default if the desired data doesn't exist.
     */
    public function get($key, $default = null) {
        if ($this->_autoStart) {
            $this->start();
        }
        
        if (!$this->_started) {
            throw new Exception\KntFrameworkException('Session not started yet.');
        }
        
        return $this->_collection->get($key, $default);
    }
    
    /**
     * Store a new data in the session or update the data identified by the specified key. 
     * The new data will be identified with the specified key.
     * 
     * @param $key The key of the data to set
     * @param $value (default null) The new value to store in the collection
     * @return Collection the current Collection 
     */
    public function set($key, $value = null) {
        if ($this->_autoStart) {
            $this->start();
        }
        
        if (!$this->_started) {
            throw new Exception\KntFrameworkException('Session not started yet.');
        }
        
        if (!is_string($key)) {
            throw new Exception\KntFrameworkException('The key must be a string.');
        }
        
        $this->_collection->set($key, $value);
        
        return $this;
    }

}
