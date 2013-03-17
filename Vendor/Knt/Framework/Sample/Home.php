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

require_once(BASE_PATH . '/Vendor/Knt/Framework/Core/IView.php');

use Knt\Framework;
use Knt\Framework\Core\IView;



 /**
  *
  */
 class View implements IView
 {

    private $method = null;
    private $query  = null;
    
    public function render($method = null) {

        $methodToRender = $method ?: $this->getMethod() ?: VIEWS_INDEX;

        if (method_exists($this, $methodToRender))
            $this->_invoke($methodToRender);

        else
            throw new Framework\Exception\KntFrameworkException(sprintf("The method '%s' was not found in the view '%s'", $methodToRender, __CLASS__));

        return $this;
    }

    protected function _invoke($method) {

        $reflection = new \ReflectionMethod($this, $method);

        if ($reflection->isPublic() && !$reflection->isAbstract()) {
            
            $args = array();

            $parameters = $reflection->getParameters();
            foreach ($parameters as $parameter) {

                if ($parameter->canBePassedByValue()) {
                    $arg = $this->query->get($parameter->getName(), null);
                    if ($arg === null && $parameter->isDefaultValueAvailable())
                        $arg = $parameter->getDefaultValue();

                    $args[] = $arg;
                } else {
                    throw new Framework\Exception\KntFrameworkException("{$parameter->getName()} cannot be passed by reference");
                }


            }

            $reflection->invokeArgs($this, $args);

        }

    }

    public function setMethod($method = null) {

        $this->method = $method ?: VIEWS_INDEX;
        return $this;

    }

    public function getMethod() {

        return $this->method ?: VIEWS_INDEX;

    }


    public function setQuery($query) {

        $this->query = $query;
        return $this;

    }

    public function getQuery() {

        return $this->query;

    }

 }

 class Home extends View {

    public function index() {
        echo 'Hello Index!';
    }

    public function test($arg1 = 'def', $arg2 = 'default') {
        echo "1:$arg1 2:$arg2";
    }


 }