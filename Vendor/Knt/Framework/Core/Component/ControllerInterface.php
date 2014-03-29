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

use
    \Knt\Framework\Core\CollectionInterface
;

interface ControllerInterface extends ComponentInterface
{
    public function call($action = null);

    public function setPostedData(CollectionInterface $data);

    public function getPostedData();
}