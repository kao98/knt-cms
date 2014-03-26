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
     * Test that component::retrieve throw an exception
     * if we didn't specify for any component and didn't set
     * default component and methods
     */
    public function testRetrieveComponent_ExceptIfNoComponentRequested() {
        $this->exception(
                function() {
                    $compName = null;
                    $methodName = null;
                    Core\Component\Component::retrieve('', BASE_PATH, $compName, $methodName);
                })->hasMessage('No component requested');
    }

    /**
     * Test that component::retrieve throw an exception
     * if we didn't specify for any method and didn't set
     * default component and methods
     */
    public function testRetrieveComponent_ExceptIfNoMethodRequested() {
        $this->exception(
                function() {
                    $compName = null;
                    $methodName = null;
                    Core\Component\Component::retrieve('/Component', BASE_PATH, $compName, $methodName);
                })->hasMessage('No method requested');
    }

    
    /**
     * Test that component::retrieve throw an exception
     * if we didn't specify a valid path
     */
    public function testRetrieveComponent_ExceptIfNoValidPath() {
        $this->exception(
                function() {
                    $compName = null;
                    $methodName = null;
                    Core\Component\Component::retrieve('', '/not/valid/path', $compName, $methodName);
                })->hasMessage('The specified path doesn\'t exists');
    }

    /**
     * Test that component::retrieve return null
     * if we didn't specify for a valid component.
     */
    public function testRetrieveComponent_ReturnNullIfNotExists() {

        $compName = '';
        $methodName = '';
        $component = Core\Component\Component::retrieve('/Component/method', BASE_PATH, $compName, $methodName);
    
        $this
            ->variable($component)
            ->isNull()
            ->variable($compName)
            ->isNull()
            ->variable($methodName)
            ->isNull();
        
    }

    /**
     * Test that component::retrieve return the correct component
     * we will look for the sample components
     */
    public function testRetrieveComponent_ReturnRightComponents() {

        $compName = '';
        $methodName = '';
        $component = Core\Component\Component::retrieve('/Home/test', SAMPLE_PATH, $compName, $methodName);
    
        $this
            ->string($component)
            ->contains('Sample/Home.php')
            ->string($compName)
            ->isEqualTo('Home')
            ->string($methodName)
            ->isEqualTo('test');
        
    }
    
    /**
     * Test that component::retrieve return the correct component
     * using the 'default method name' parameter
     * we will look for the sample components
     */
    public function testRetrieveComponent_ReturnRightComponents_WithDefaultMethod() {

        $compName = '';
        $methodName = '';
        $component = Core\Component\Component::retrieve('/Home', SAMPLE_PATH, $compName, $methodName, '.php', null, 'test');
    
        $this
            ->string($component)
            ->contains('Sample/Home.php')
            ->string($compName)
            ->isEqualTo('Home')
            ->string($methodName)
            ->isEqualTo('test');
        
    }
    
    /**
     * Test that component::retrieve return the correct component
     * using the 'default component name' parameter
     * we will look for the sample components
     */
    public function testRetrieveComponent_ReturnRightComponents_WithDefaultComponent() {

        $compName = '';
        $methodName = '';
        $component = Core\Component\Component::retrieve('/', SAMPLE_PATH, $compName, $methodName, '.php', 'Home', 'test');
    
        $this
            ->string($component)
            ->contains('Sample/Home.php')
            ->string($compName)
            ->isEqualTo('Home')
            ->string($methodName)
            ->isEqualTo('test');
        
    }
    
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
    
    /**
     * We can't invoke a method with arguments that are passed by reference
     */
    public function testInvoke_exceptIfArgumentPassedByReference() {
        
        $frameworkMock = new \mock\Knt\Framework\Framework;
        $collectionMock = new \mock\Knt\Framework\Core\CollectionInterface;
        
        $compName = '';
        $methodName = '';
        $componentFile = Core\Component\Component::retrieve('/Home/referencedParameter', SAMPLE_PATH, $compName, $methodName);
        require ($componentFile);
        $compName = PROJECT_NAMESPACE . $compName;
        $component = new $compName($frameworkMock, $methodName, $collectionMock);
        
        $this
            ->exception(
                        function() use($component) {
                            $component();
                        }
                    )
                    ->hasMessage('arg cannot be passed by reference');
        
    }
    
    /**
     * We can't invoke a private method
     */
    public function testInvoke_exceptIfNotPublic() {
        
        $frameworkMock = new \mock\Knt\Framework\Framework;
        $collectionMock = new \mock\Knt\Framework\Core\CollectionInterface;
        
        $compName = '';
        $methodName = '';
        $componentFile = Core\Component\Component::retrieve('/Home/privateMethod', SAMPLE_PATH, $compName, $methodName);
        require ($componentFile);
        $compName = PROJECT_NAMESPACE . $compName;
        $component = new $compName($frameworkMock, $methodName, $collectionMock);
        
        $this
            ->exception(
                        function() use($component) {
                            $component();
                        }
                    )
                    ->hasMessage('You are not authorized to call Knt\Framework\Sample\Home::privateMethod');
        
        $component->setMethod('protectedMethod');
        
        $this
            ->exception(
                        function() use($component) {
                            $component();
                        }
                    )
                    ->hasMessage('You are not authorized to call Knt\Framework\Sample\Home::protectedMethod');
        
                    
    }
    
}