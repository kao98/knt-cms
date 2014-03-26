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
use \Knt\Framework\Framework;
use \Knt\Framework\Core\CollectionInterface;

/**
 * View.php
 * Creation date: 19 mar. 2013
 * 
 * KNT View class.
 * Base class for the views to render
 * 
 * Version 1.0: Initial version
 *
 * @package Knt\Framework\View
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
class View extends Component implements ViewInterface
{
    
     /**
     * Constructor. Initialize the View.
     * 
     * @param Framework\Framework $frameworkInstance an instance ofthe framework
     * @param string $method (default null) the name of the method to call to render the View.
     * If null the View will be initialized with the VIEWS_INDEX method.
     * @return View the current View instance 
     */
    public function __construct(Framework $frameworkInstance, $method = null, CollectionInterface $queriedData = null) {
        
        parent::__construct(
                $frameworkInstance, 
                $method ?: VIEWS_INDEX, 
                $queriedData ?: $frameworkInstance->getRequest()->getQueriedData()
                );
        
    }
    
    /**
     * Render the view: by calling the specified method
     * 
     * @param string $method (default null) the method to call.
     * If null, will call the method specified during initialization of the class.
     * If no method has been specified at all, will try to call the VIEWS_INDEX method.
     * @return View the current View instance
     */
    public function render($method = null) {

        $methodToRender = $method ?: $this->getMethod() ?: VIEWS_INDEX;

        $this->invoke($methodToRender);

        return $this;
    }


    public function setQueriedData(CollectionInterface $data) {
        
        return $this->setData($data);

    }

    public function getQueriedData() {

        return $this->getData();

    }
    
 }
