<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class stickde3 extends eqLogic {
 
  public function preUpdate() {
    if ($this->getConfiguration('addr') == '') {
      throw new Exception(__('L\'adresse ne peut être vide',__FILE__));
    }
    if ($this->getConfiguration('port') == '') {
      throw new Exception(__('Le port ne peut être vide',__FILE__));
    }
  }

  public function preSave() {
    $this->setLogicalId($this->getConfiguration('addr'));
  }

  public function postSave() {
  }

  public function postUpdate() {
      $stickde3Cmd = $this->getCmd(null, 'last');
  if (!is_object($stickde3Cmd)) {
			$stickde3Cmd = new stickde3Cmd();
		}
		$stickde3Cmd->setName(__('Dernière commande', __FILE__));
		$stickde3Cmd->setLogicalId('last');
		$stickde3Cmd->setEqLogic_id($this->getId());
		$stickde3Cmd->setType('info');
		$stickde3Cmd->setSubType('string');
		$stickde3Cmd->save();
  }
    public function callstickde3($_url) {
    $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
      log::add('stickde3', 'debug', 'Commande avant conversion:' . $_url);  
    $_url = hex2bin($_url);
    $len = strlen($_url);
    socket_sendto($sock, $_url, $len, 0 ,$this->getConfiguration('addr'), $this->getConfiguration('port'));
    $result = 'good';
    log::add('stickde3', 'debug', 'Commande envoyée: ' . $_url . ' adresse: '. $this->getConfiguration('addr') . ' port: ' . $this->getConfiguration('port'));  

    socket_close($sock);
    return $result;
  }
  

}


class stickde3Cmd extends cmd {
  public function execute($_options = array()) {
      $eqLogic = $this->getEqLogic();
    /*switch ($this->getType()) {
      case 'action' :*/
        $eqLogic->callstickde3($this->getConfiguration('commande'));

        $eqLogic->setConfiguration('last', 'ff');
       /* break;
    }*/
  }
}
  ?>
