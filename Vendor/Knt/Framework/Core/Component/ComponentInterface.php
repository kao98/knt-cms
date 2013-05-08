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

namespace Knt\Framework\Core\Component;
use Knt\Framework\Framework;
use Knt\Framework\Core\CollectionInterface;

/**
 * ComponentInterface.php
 * Creation date: 03 may 2013
 * 
 * KNT Component interface.
 * Base interface for some components as Views or Controllers
 * 
 * Version 1.0: Initial version
 *
 * @package Knt\Framework\Core
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
 interface ComponentInterface
 {

    /**
     * Initialize the Component.
     * 
     * @param Framework\Framework $frameworkInstance an instance ofthe framework
     * @param string $method the name of the method of the component to call
     * @param Framework\Core\CollectionInterface $data a collection of data to pass to the component.
     * Those data will be bind to the method arguments
     * @return Component the current Component instance 
     */
    public function initialize(Framework $frameworkInstance, $method, CollectionInterface $data);

    /**
     * Invoke the specified method of the current component.
     * If the method as some arguments, we will try to bind them with the component datas.
     * 
     * @param string $method the method of the component to invoke
     */
    public function invoke($method);
    
    public function setMethod($method);

    public function getMethod();

    public function setData(CollectionInterface $data);

    public function getData();
    
 }
