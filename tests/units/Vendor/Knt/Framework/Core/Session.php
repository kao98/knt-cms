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
 * Tests for the Request class.
 * Last version tested: 1.0
 */

use \Knt\Framework\Core;

class Session extends atoum\test
{
    /**
     * To test the constructor without parameters.
     * It should initialize the request with $_REQUEST object
     */
    public function testConstructor_WithoutParameters_InitializeDefaultSession() {
        $sess = new Core\Session();
        $this->string($sess->getName())->isEqualTo(ini_get('session.name'));
    }

    /**
     * Test the start method.
     * It should start the collection and allow us
     * to use it.
     */
    public function testStart() {
        
        $sess = new Core\Session('PHPSESSID', false);
        $sess->start();
        
        $this
            ->string(
                $sess
                    ->set('foo', 'bar')
                    ->get('foo'))
            ->isEqualTo('bar');
        
    }
    
    /**
     * Test that we have to manually start the session if 
     * we didn't set autostart to true
     */
    public function testSessionMustBeStartedBeforeUse() {
        
        $sess = new Core\Session('PHPSESSID', false);
        
        $this
            ->exception(
                function() use($sess) {
                    $sess->set('foo', 'bar');
                }
            )
            ->hasMessage('Session not started yet.');
        
        $this
            ->exception(
                function() use($sess) {
                    $sess->get('foo', 'bar');
                }
            )
            ->hasMessage('Session not started yet.');
        
            
        $sess->start();
        $this
            ->string(
                $sess
                    ->set('foo', 'bar')
                    ->get('foo'))
            ->isEqualTo('bar');
            
    }
    
    /**
     * Test that setName and getName works properly
     */
    public function testSetName() {
        $sessionName = 'SessionName';
        $sess = new Core\Session();
        $sess->setName($sessionName);
        $this
            ->string($sess->getName())
            ->isEqualTo(session_name())
            ->isEqualTo($sessionName);
    }
    
    /**
     * test the nominal uses of the session (use of set and get methods, and chaining methods)
     */
    public function testNominalUse() {
        
        $sess = new Core\Session();
        $this
            ->string($sess->set('foo', 'bar')->get('foo'))
            ->isEqualTo($_SESSION['foo'])
            ->isEqualTo('bar');
        
    }
    
    /**
     * Test that we cannot use the set method with an integer key.
     */
    public function testSet_KeyMustBeAString() {
        $sess = new Core\Session();
        $this
            ->exception(
                function() use($sess) {
                    $sess->set(1, 'test');
                }
            )
            ->hasMessage('The key must be a string.');
    }
    
}