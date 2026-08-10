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

namespace vcms\module;

class LibAccessRestriction{
	var $gruppen;
	var $aemter;
	var $rechte;

	function __construct($gruppen, $aemter, $rechte = ''){
		$this->gruppen = $gruppen;
		$this->aemter = $aemter;
		$this->rechte = $rechte;
	}

	function getGruppen(){
		return $this->gruppen;
	}

	/*
	* Veraltete Notation: Rollen von Chargen statt Rechte.
	*/
	function getAemter(){
		return $this->aemter;
	}

	function getRechte(){
		return $this->rechte;
	}

	function hasGruppenRestriction(){
		return is_array($this->gruppen) && count($this->gruppen) > 0;
	}

	function hasAemterRestriction(){
		return is_array($this->aemter) && count($this->aemter) > 0;
	}

	function hasRechteRestriction(){
		return is_array($this->rechte) && count($this->rechte) > 0;
	}

	function isFulfilledBy($gruppe, $rollen, $rechte){
		$gruppenOk = false;

		//should this restriction be restricted by group membership?
		if($this->hasGruppenRestriction()){
			if(in_array($gruppe, $this->gruppen)){
				$gruppenOk = true;
			}
		} else {
			$gruppenOk = true;
		}

		//ohne Einschränkung auf Rechte oder Rollen genügt die Gruppe
		if(!$this->hasRechteRestriction() && !$this->hasAemterRestriction()){
			return $gruppenOk;
		}

		$funktionOk = false;

		if($this->hasRechteRestriction() && is_array($rechte)){
			if(count(array_intersect($rechte, $this->rechte)) > 0){
				$funktionOk = true;
			}
		}

		//veraltete Notation: gegen die Rollen der gehaltenen Chargen prüfen
		if(!$funktionOk && $this->hasAemterRestriction() && is_array($rollen)){
			if(count(array_intersect($rollen, $this->aemter)) > 0){
				$funktionOk = true;
			}
		}

		return $gruppenOk && $funktionOk;
	}
}