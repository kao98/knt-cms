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


namespace Knt\Framework\Tests\Units\Core;

use \mageekguy\atoum;

 /**
 * Tests for the Collection class.
 * Last version tested: 1.0
 * 
 * @lastUpdate 2012-11-28
 */

use \Knt\Framework\Core;

class Collection extends atoum\test
{

    // ... not sure the following tests are relevant               ... //
    // ... tests of the constructor and interfaces implementations ... //

    /**
     * To test the constructor without parameters.
     * It should initialize an empty collection
     */
    public function testConstructor_WithoutParameters_InitializeAnEmptyCollection() {
        $col = new Core\Collection();
        $this->object($col)->hasSize(0);
    }

    /**
     * To test the constructor with some parameters
     * It should initialize the collection with the given data.
     */
    public function testConstructor_WithParameters_InitializeTheCollection() {
        $arr = array(0, 1);
        $col = new Core\Collection($arr);
        $this->object($col)->hasSize(2);
    }

    /**
     * Test that the collection don't alter the initial array
     * if we didn't ask to use a reference
     */
    public function testConstructor_ByValue() {
        $arr = array(0, 1);
        $col = new Core\Collection($arr);
        $col->set(0, 2);
        $this->integer($col->get(0))->isEqualTo(2)
             ->integer($arr[0])->isEqualTo(0);
    }

    /**
     * Test that the collection alter the initial array
     * if we ask to use a reference
     */
    public function testConstructor_ByReference() {
        $arr = array(0, 1);
        $col = new Core\Collection($arr, true);
        $col->set(0, 2);
        $this->integer($col->get(0))->isEqualTo(2)
             ->integer($arr[0])->isEqualTo(2);
    }

    /**
     * To test the IteratorAggregate implementation
     * We iterate the collection
     */
    public function testIterator() {
        $array = array(0, 1, 3);
        $col = new Core\Collection($array);
        foreach ($col as $key => $value)
            $this->integer($value)->isEqualTo($array[$key]);
    }

    /**
     * To test the implementation of the Countable interface
     */
    public function testCount() {
        $array = array(0, 1, 2, 3);
        $col = new Core\Collection($array);
        $this->integer(count($col))->isEqualTo(4);
    }

    // ... here comes the relevant tests (I guess) ... //

    /**
     * Test of the method Add that is supposed to add a new value in the collection
     */
    public function testAdd() {
        
        //Test: Add on an empty collection
        $col = new Core\Collection();
        $this->integer($col->add('test 0'))->isEqualTo(0);
        
        //Test: Add on a mixed collection
        $array = array(5 => 0, 10 => 1, 'a' => 2);
        $col = new Core\Collection($array);
        
        $this->integer($col->add('test 1'))->isEqualTo(11);
        $this->integer($col->add('test 2'))->isEqualTo(12);

    }

    /**
     * Test of the method Set that set a value in the collection
     */
    public function testSet() {
        $array = array(0 => 0, "1" => "1");
        $col = new Core\Collection($array);
        $this->integer($col->set(0, 10)->get(0))->isEqualTo(10);
        $this->string($col->set("1", "test")->get("1"))->isEqualTo("test");
        $this->string($col->set("new index", "new test")->get("new index"))->isEqualTo("new test");
    }

    /**
     * Test of the method Get
     */
    public function testGet() {
        $array = array(0 => 0, "1" => "1");
        $col = new Core\Collection($array);
        $this->integer($col->get(0))->isEqualTo(0);
        $this->string($col->get("1"))->isEqualTo("1");
        $this->variable($col->get(2, 2))->isEqualTo(2);
        $this->variable($col->get(3))->isNull();

        //some deep search
        $col->set("deepArray", array(1 => array("intoDeep" => 2)));
        $this->integer($col->get("deepArray[1]['intoDeep']"))->isEqualTo(2);
        $this->integer($col->get('deepArray[1]["intoDeep"]'))->isEqualTo(2);
        $this->variable($col->get('deepArray[2][intoDeep]'))->isNull();
        $this->variable($col->get('0[0]'))->isNull();

        //To be sure to validate the _deepSearch method as in the documentation example (cf _deepSearch documentation)
        $array = array(0 => array(0 => array(1 => 1)));
        $col = new Core\Collection($array);
        $this->variable($col->get('0'))->isEqualTo(array(0 => array(1 => 1)));
        $this->variable($col->get('0[0]'))->isEqualTo(array(1 => 1));
        $this->variable($col->get('0[0][1]'))->isEqualTo(1);
        $this->variable($col->get('0[1]'))->isNull();
        $this->variable($col->get('0[1]', 0))->isEqualTo(0);

        $this->variable($col->get('0[0][1][1]'))->isNull();

    }

    /**
     * Test the serialization of the Collection
     */
    public function testSerialization() {
        $arr = array(0, "Un", 'a' => array(2));
        $initialCol = new Core\Collection($arr);
        $resultCol = new Core\Collection();
        $resultCol->unserialize($initialCol->serialize());

        $this->object($resultCol)->isEqualTo($initialCol);

    }

}