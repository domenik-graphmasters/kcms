<?php
/*
This file is part of VCMS.

VCMS is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

VCMS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with VCMS. If not, see <http://www.gnu.org/licenses/>.
*/

namespace vcms;

use PDO;

class LibSecurityManager{
	const RECHT_SYSTEMVERWALTUNG = 'systemverwaltung';
	const RECHT_STAMMDATEN = 'stammdaten';
	const RECHT_MITGLIEDERVERWALTUNG = 'mitgliederverwaltung';
	const RECHT_BENUTZERKONTEN = 'benutzerkonten';
	const RECHT_GALERIEVERWALTUNG = 'galerieverwaltung';

	/*
	* Die Rechte der Engine. Module dürfen in ihrer meta.json eigene Rechte
	* verwenden; die Chargenverwaltung sammelt sie aus den installierten Modulen ein.
	*/
	var $possibleRechte = array(
			self::RECHT_SYSTEMVERWALTUNG,
			self::RECHT_STAMMDATEN,
			self::RECHT_MITGLIEDERVERWALTUNG,
			self::RECHT_BENUTZERKONTEN,
			self::RECHT_GALERIEVERWALTUNG);

	/*
	* Veraltet: die Rollen der Chargen, also die alten Amtsbezeichnungen. Werden nur
	* noch für Module gebraucht, deren meta.json weiterhin aemter statt rechte angibt.
	*/
	var $possibleAemter = array(
			'senior', 'consenior', 'fuchsmajor', 'fuchsmajor2', 'scriptor', 'quaestor', 'jubelsenior',
			'ahv_senior', 'ahv_consenior', 'ahv_keilbeauftragter', 'ahv_scriptor', 'ahv_quaestor', 'ahv_beisitzer1', 'ahv_beisitzer2',
			'hv_vorsitzender', 'hv_kassierer', 'hv_beisitzer1', 'hv_beisitzer2',
			'archivar', 'ausflugswart',
			'bierwart', 'bootshauswart',
			'couleurartikelwart',
			'dachverbandsberichterstatter', 'datenpflegewart',
			'fechtwart', 'ferienordner', 'fotowart',
			'hauswart', 'huettenwart',
			'internetwart',
			'kuehlschrankwart',
			'musikwart',
			'redaktionswart',
			'sportwart', 'stammtischwart',
			'technikwart', 'thekenwart',
			'wirtschaftskassenwart', 'wichswart',
			'vop', 'vvop', 'vopxx', 'vopxxx', 'vopxxxx');

	function getPossibleAemter(){
		return $this->possibleAemter;
	}

	function getPossibleRechte(){
		return $this->possibleRechte;
	}

	/*
	* Alle Rechte, die sich einer Charge zuweisen lassen: die der Engine, die in den
	* meta.json installierter Module verwendeten und die bereits vergebenen.
	*
	* ponytail: Rechte, die ein Modul ausschließlich im Code prüft, kommen über die
	* bereits vergebenen herein - der Seed des Moduls muss sie also einmal vergeben.
	* Ein eigenes Feld in der meta.json lohnt erst, wenn das nicht mehr reicht.
	*/
	function getVergebbareRechte(){
		global $libModuleHandler, $libCharge;

		$rechte = $this->possibleRechte;

		if(is_object($libModuleHandler)){
			foreach($libModuleHandler->getModules() as $module){
				$elemente = array_merge($module->getPages(), $module->getIncludes());

				foreach($elemente as $element){
					if(!$element->hasAccessRestriction()){
						continue;
					}

					$elementRechte = $element->getAccessRestriction()->getRechte();

					if(is_array($elementRechte)){
						$rechte = array_merge($rechte, $elementRechte);
					}
				}
			}
		}

		if(is_object($libCharge)){
			$rechte = array_merge($rechte, $libCharge->getVergebeneRechte());
		}

		$rechte = array_values(array_unique($rechte));
		sort($rechte);

		return $rechte;
	}

	function hasAccess($libElement, $libAuth){
		//public page?
		if(!$libElement->hasAccessRestriction()){
			return true;
		} else { //internal page?
			//not logged in?
			if(!$libAuth->isLoggedIn()){
				return false;
			}

			$accessRestriction = $libElement->getAccessRestriction();

			//enough rights?
			if($accessRestriction->isFulfilledBy($libAuth->getGruppe(), $libAuth->getRollen(), $libAuth->getRechte())){
				return true;
			} else {
				return false;
			}
		}
	}

	function generateAggregatedAccessRestriction($accessRestrictions){
		return new \vcms\module\LibAccessRestriction(
			$this->aggregateRestrictionValues($accessRestrictions, 'getGruppen'),
			$this->aggregateRestrictionValues($accessRestrictions, 'getAemter'),
			$this->aggregateRestrictionValues($accessRestrictions, 'getRechte'));
	}

	/*
	* Vereinigt eine Dimension über alle Restriktionen. Sobald ein Element ohne
	* Einschränkung dabei ist, entfällt der Filter für diese Dimension.
	*/
	function aggregateRestrictionValues($accessRestrictions, $getter){
		$included = array();

		//no foreach, as otherwise php4 does copy-by-value!
		for($i=0; $i<count($accessRestrictions); $i++){
			$values = $accessRestrictions[$i]->$getter();

			//element without restriction? then no filter remains
			if(!is_array($values) || count($values) == 0){
				return '';
			}

			$included = array_merge($included, $values);
		}

		if(count($included) == 0){
			return '';
		}

		return array_values(array_unique($included));
	}
}
