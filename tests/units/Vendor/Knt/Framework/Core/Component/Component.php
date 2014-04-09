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
 * Tests for the Request class.
 * Last version tested: 1.0
 */

use \Knt\Framework\Core;

define('SAMPLE_PATH', BASE_PATH . '/Vendor/Knt/Framework/Sample');

class Component extends atoum\test
{
    /**
     * Test the constructor and the accessors of the Component class
     * Also test method chaining
     */
    public function testConstructorAndAccessors() {
        
        $frameworkMock = new \mock\Knt\Framework\Framework;
        $collectionMock = new \mock\Knt\Framework\Core\Collection;
        $collectionMock2 = new \mock\Knt\Framework\Core\Collection;
        
        $component = new Core\Component\Component($frameworkMock, 'method', $collectionMock);
        $this
            ->object($component)->isCallable()
            ->string($component->getMethod())->isEqualTo('method')
            ->object($component->getData())->isEqualTo($collectionMock);
        
        $this
            ->string($component->setMethod('foo')->getMethod())->isEqualTo('foo')
            ->object($component->setData($collectionMock2)->getData())->isEqualTo($collectionMock2);
        
    }
    
    /**
     * Test the Invoke method throw an exception if no method to invoke
     */
    public function testInvoke_exceptIfMethodDontExists() {
        
        $frameworkMock = new \mock\Knt\Framework\Framework;
        $collectionMock = new \mock\Knt\Framework\Core\Collection;
        
        $component = new Core\Component\Component($frameworkMock, 'method', $collectionMock);
        
        $this->exception(
                    function() use($component) {
                        $component->invoke('method');
                    }
                )
                ->hasMessage("Component 'Knt\Framework\Core\Component\Component' has no method 'method'");
        
        $this->exception(
                    function() use($component) {
                        $component->setMethod(null);
                        $component();
                    }
                )
                ->hasMessage("Method to invoke is missing.");
        
    }
    
    /**
     * Test the nominal use of the Invoke method.
     */
    public function testInvoke_nominalUse() {
        
        $parameters = array('method' => 'getData');
        $frameworkMock = new \mock\Knt\Framework\Framework;
        $collection = new \Knt\Framework\Core\Collection($parameters);
        
        //We will invoke the setMethod method that will update the method
        //from 'setMethod' to 'getData'.
        
        $component = new Core\Component\Component($frameworkMock, 'setMethod', $collection);
        $component->invoke('setMethod');
        
        $this
            ->string($component->getMethod())->isEqualTo('getData');
        
    }
    
    /**
     * Same test as testInvoke_nominalUse but using the __invoke magic method
     */
    public function testCallable_nominalUse() {
        
        $parameters = array('method' => 'getData');
        $frameworkMock = new \mock\Knt\Framework\Framework;
        $collection = new \Knt\Framework\Core\Collection($parameters);
        
        //We will invoke the setMethod method that will update the method
        //from 'setMethod' to 'getData'.
        
        $component = new Core\Component\Component($frameworkMock, 'setMethod', $collection);
        $component();
        
        $this
            ->string($component->getMethod())->isEqualTo('getData');
        
    }
    
    
}