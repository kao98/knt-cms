<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Knt\Framework\Core\Routeur;

/**
 *
 * @author Aurelien
 */
interface RouteInterface {
    //put your code here
    public function getUri();
    public function getComponentName();
    public function getMethodName();
}
