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


namespace Knt\Framework\Tests\Units\Core\Component;

use \mageekguy\atoum;

 /**
 * Tests for the Controller class.
 * Last version tested: 1.0
 */

use \Knt\Framework\Core;

class Controller extends atoum\test
{
    /**
     * To test the constructor without parameters.
     * It should initialize the request with $_REQUEST object
     */
    public function testRetrieveController() {
        $this->exception(
                function() {
                    $compName = null;
                    $methodName = null;
                    Core\Component\Controller::retrieve('', BASE_PATH, $compName, $methodName);
                })->hasMessage('No component requested');
    }

}