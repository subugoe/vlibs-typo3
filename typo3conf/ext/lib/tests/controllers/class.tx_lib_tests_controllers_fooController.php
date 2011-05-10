<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2006 Elmar Hinz
 *  Contact: elmar.hinz@team-red.net
 *  All rights reserved
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 ***************************************************************/

require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_controller.php');
class tx_lib_tests_controllers_fooController extends tx_lib_controller{

    // own action
    function fooAction($out, $conf, $vars){
        return 'Foo->foo';
    }
    // overwritten action
    function barAction($out, $conf,$vars){
        return 'Foo->bar';
    }

    function parentSerializeParametersAction($out, $conf,$vars){
        return $out . serialize($conf) . serialize($vars);
    }

}

?>