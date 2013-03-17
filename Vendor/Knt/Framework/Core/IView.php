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
 * IView.php
 * Creation date: 16 mar. 2013
 * 
 * KNT IView interface.
 * Base interface for the views managed by the framework.
 * 
 * Version 1.0: Initial version
 *
 * @package Knt\Framework\Core
 * @version 1.0
 * @author Aurélien Reeves (Kao ..98)
 */
interface IView
{

    /**
     * Render: render the view.
     * 
     */
    public function render($method = null);

}