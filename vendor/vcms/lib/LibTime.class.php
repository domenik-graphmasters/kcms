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

use DateTime;
use DateTimeZone;

class LibTime{

	function getSemestersConfig(){
		global $libConfig;

		//does the config contain a semester config?
		if(isset($libConfig->semestersConfig) && is_array($libConfig->semestersConfig)){
			$lengthOk = true;

			foreach($libConfig->semestersConfig as $year){
				foreach($year as $month){
					if(strlen($month) != 2){
						$lengthOk = false;
					}
				}
			}

			if($lengthOk){
				ksort($libConfig->semestersConfig);

				if(isset($libConfig->semestersConfig[0])){
					return $libConfig->semestersConfig;
				}
			}
		}

		return array(0 => array('WS', 'WS', 'WS', 'SS', 'SS', 'SS', 'SS', 'SS', 'SS', 'WS', 'WS', 'WS'));
	}

	//--------------- prefix operations on years -----------------------------------

	function getSemesterPrefixesOfYear($year){
		$semesterPrefixesOfYear = array();

		foreach($this->getSemestersConfig() as $key => $value){
			if($key <= ((int) $year)){
				$semesterPrefixesOfYear = $value;
			}
		}

		return $semesterPrefixesOfYear;
	}

	function findPrefixBlocks($semesterPrefixes){
		$prefixBlocks = array();
		$lastPrefix = '';

		//iterate over prefixes
		for($i=0; $i<12; $i++){
			//is there a semester defined for the month $i in this year?
			if(isset($semesterPrefixes[$i])){
				$prefix = $semesterPrefixes[$i];

				if($prefix != $lastPrefix){
					$prefixBlocks[] = array('month' => $i, 'prefix' => $prefix);
					$lastPrefix = $prefix;
				}
			}
		}

		return $prefixBlocks;
	}

	//------------- semester operations on years -----------------------------------------

	function getSemestersInYear($year){
		//remove zerofilling: 0001 -> 1
		$year = (int) $year;
		$semesters = array();

		if(!is_numeric($year)){
			return $semesters;
		}

		$yearName = $this->zeroFill($year, 4);

		$prefixes = $this->getSemesterPrefixesOfYear($year);
		$prefixBlocks = $this->findPrefixBlocks($prefixes);
		$numberOfPrefixBlocks = count($prefixBlocks);

		//only one semester in this year?
		if($numberOfPrefixBlocks == 1){
			$semesters[$prefixes[0].$yearName] = array(
				'name' => $prefixes[0].$yearName,
				'prefix' => $prefixes[0],
				'overlapping' => false,
				'startdate' => $yearName.'-01-01',
				'startyear' => $yearName,
				'enddate' => $yearName.'-12-31',
				'endyear' => $yearName,
				'months' => range(0, 11));
		}
		//multiple semesters in this year
		else {
			$lastYearPrefixBlocks = $this->findPrefixBlocks($this->getSemesterPrefixesOfYear($year - 1));
			$nextYearPrefixBlocks = $this->findPrefixBlocks($this->getSemesterPrefixesOfYear($year + 1));

			//iterate over prefix blocks / semesters of this year
			for($i = 0; $i < $numberOfPrefixBlocks; $i++){
				$prefixBlock = $prefixBlocks[$i];
				$month = $prefixBlock['month'];
				$prefix = $prefixBlock['prefix'];

				//first semester in this year?
				if($i == 0){
					//last year had only one semester?
					if(count($lastYearPrefixBlocks) == 1){
						//-> this is a new non-overlapping semester
						$startMonth = 0;
						$endMonth = $prefixBlocks[$i+1]['month']-1;
						$startMonthName = $this->zeroFill($startMonth+1);
						$endMonthName = $this->zeroFill($endMonth+1);
						$numberOfDaysOfEndMonth = $this->getNumberOfDaysInMonth($year, $endMonthName);
						$semesters[$prefix.$yearName] = array(
							'name' => $prefix.$yearName,
							'prefix' => $prefix,
							'overlapping' => false,
							'startdate' => $yearName.'-'.$startMonthName.'-01',
							'startyear' => $yearName,
							'enddate' => $yearName.'-'.$endMonthName.'-'.$numberOfDaysOfEndMonth,
							'endyear' => $yearName,
							'months' => range($startMonth, $endMonth));
					}
					//last year had multiple semesters
					else {
						//-> this prefixblock could be part of an overlapping semester
						if(count($lastYearPrefixBlocks) > 0){ //last year has at least one semester? not year 1 before christ?
							$lastYearLastPrefixBlock = $lastYearPrefixBlocks[count($lastYearPrefixBlocks)-1];

							//this prefixblock has the same prefix as the last year's last one?
							if($lastYearLastPrefixBlock['prefix'] == $prefix){
								//-> this prefixblock is part of an overlapping semester
								$lastYear = $year - 1;
								$lastYearName = $this->zeroFill($lastYear, 4);
								$startMonth = $lastYearLastPrefixBlock['month'];
								$endMonth = $prefixBlocks[$i+1]['month']-1;
								$startMonthName = $this->zeroFill($startMonth+1);
								$endMonthName = $this->zeroFill($endMonth+1);
								$numberOfDaysOfEndMonth = $this->getNumberOfDaysInMonth($year, $endMonthName);
								$semesters[$prefix.$lastYearName.$yearName] = array(
									'name' => $prefix.$lastYearName.$yearName,
									'prefix' => $prefix,
									'overlapping' => true,
									'startdate' => $lastYearName.'-'.$startMonthName.'-01',
									'startyear' => $lastYearName,
									'enddate' => $yearName.'-'.$endMonthName.'-'.$numberOfDaysOfEndMonth,
									'endyear' => $yearName,
									'months' => array_merge(range($startMonth, 11), range(0, $endMonth)));
							}
							//this prefixblock is a new semester (not a semester overlapping from last year)
							else {
								$startMonth = $month;
								$endMonth = $prefixBlocks[$i+1]['month']-1;
								$startMonthName = $this->zeroFill($startMonth + 1);
								$endMonthName = $this->zeroFill($endMonth+1);
								$numberOfDaysOfEndMonth = $this->getNumberOfDaysInMonth($year, $endMonthName);
								$semesters[$prefix.$yearName] = array(
									'name' => $prefix.$yearName,
									'prefix' => $prefix,
									'overlapping' => false,
									'startdate' => $yearName.'-'.$startMonthName.'-01',
									'startyear' => $yearName,
									'enddate' => $yearName.'-'.$endMonthName.'-'.$numberOfDaysOfEndMonth,
									'endyear' => $yearName,
									'months' => range($startMonth, $endMonth));
							}
						}
					}
				}
				//last semester in this year?
				elseif($i == ($numberOfPrefixBlocks-1)){
					//next year has only one semester?
					if(count($nextYearPrefixBlocks) == 1){
						//-> this is a non-overlapping semester
						$startMonth = $month;
						$endMonth = 11;
						$startMonthName = $this->zeroFill($startMonth + 1);
						$endMonthName = $this->zeroFill($endMonth+1);
						$numberOfDaysOfEndMonth = $this->getNumberOfDaysInMonth($year, $endMonthName);
						$semesters[$prefix.$yearName] = array(
							'name' => $prefix.$yearName,
							'prefix' => $prefix,
							'overlapping' => false,
							'startdate' => $yearName.'-'.$startMonthName.'-01',
							'startyear' => $yearName,
							'enddate' => $yearName.'-'.$endMonthName.'-'.$numberOfDaysOfEndMonth,
							'endyear' => $yearName,
							'months' => range($startMonth, $endMonth));
					}
					//next year has multiple semesters
					else{
						//-> this prefixblock could be part of an overlapping semester
						$nextYearFirstPrefixBlock = $nextYearPrefixBlocks[0];

						//this prefixblock has the same prefix as the next year's first one?
						if($nextYearFirstPrefixBlock['prefix'] == $prefix){
							//-> this prefixblock is part of an overlapping semester
							$nextYear = $year + 1;
							$nextYearName = $this->zeroFill($nextYear, 4);
							$startMonth = $month;
							$endMonth = $nextYearPrefixBlocks[1]['month']-1;
							$startMonthName = $this->zeroFill($startMonth+1);
							$endMonthName = $this->zeroFill($endMonth+1);
							$numberOfDaysOfEndMonth = $this->getNumberOfDaysInMonth($nextYear, $endMonthName);
							$semesters[$prefix.$yearName.$nextYearName] = array(
								'name' => $prefix.$yearName.$nextYearName,
								'prefix' => $prefix,
								'overlapping' => true,
								'startdate' => $yearName.'-'.$startMonthName.'-01',
								'startyear' => $yearName,
								'enddate' => $nextYearName.'-'.$endMonthName.'-'.$numberOfDaysOfEndMonth,
								'endyear' => $nextYearName,
								'months' => array_merge(range($startMonth, 11), range(0, $endMonth)));
						}
						//this prefixblock is not overlapping into next year
						else {
							$startMonth = $month;
							$endMonth = 11;
							$startMonthName = $this->zeroFill($startMonth+1);
							$endMonthName = $this->zeroFill($endMonth+1);
							$numberOfDaysOfEndMonth = $this->getNumberOfDaysInMonth($year, $endMonthName);
							$semesters[$prefix.$yearName] = array(
								'name' => $prefix.$yearName,
								'prefix' => $prefix,
								'overlapping' => false,
								'startdate' => $yearName.'-'.$startMonthName.'-01',
								'startyear' => $yearName,
								'enddate' => $yearName.'-'.$endMonthName.'-'.$numberOfDaysOfEndMonth,
								'endyear' => $yearName,
								'months' => range($startMonth, $endMonth));
						}
					}
				}
				//middle semester -> not overlapping
				else {
					$startMonth = $month;
					$endMonth = $prefixBlocks[$i+1]['month']-1;
					$startMonthName = $this->zeroFill($startMonth+1);
					$endMonthName = $this->zeroFill($endMonth+1);
					$numberOfDaysOfEndMonth = $this->getNumberOfDaysInMonth($year, $endMonthName);
					$semesters[$prefix.$yearName] = array(
						'name' => $prefix.$yearName,
						'prefix' => $prefix,
						'overlapping' => false,
						'startdate' => $yearName.'-'.$startMonthName.'-01',
						'startyear' => $yearName,
						'enddate' => $yearName.'-'.$endMonthName.'-'.$numberOfDaysOfEndMonth,
						'endyear' => $yearName,
						'months' => range($startMonth, $endMonth));
				}
			}
		}

		//the first semester ever has to have the start date 0000-00-00
		$yearsInSemestersConfig = array_keys($this->getSemestersConfig());
		$firstYearInSemestersConfig = $yearsInSemestersConfig[0];

		//first year ever?
		if($year == ((int) $firstYearInSemestersConfig)){
			$semesterKeys = array_keys($semesters);
			$firstSemesterKey = $semesterKeys[0];
			$semesters[$firstSemesterKey]['startdate'] = '0000-00-00';
			$semesters[$firstSemesterKey]['startyear'] = '0000';
		}

		return $semesters;
	}

	function getSemestersStartingInYear($year){
		$semestersStartingInYear = array();

		foreach($this->getSemestersInYear($year) as $name => $semester){
			if($semester['startyear'] == $year || ((int) $semester['startyear']) == ((int) $year)){
				$semestersStartingInYear[$name] = $semester;
			}
		}

		return $semestersStartingInYear;
	}

	function getSemestersEndingInYear($year){
		$semestersEndingInYear = array();

		foreach($this->getSemestersInYear($year) as $name => $semester){
			if($semester['endyear'] == $year || ((int) $semester['endyear']) == ((int) $year)){
				$semestersEndingInYear[$name] = $semester;
			}
		}

		return $semestersEndingInYear;
	}

	//--------------------------- operations on semester string giving semester array --------------------------------------------

	function isValidSemesterString($semesterString){
		$regexp = "/([a-zA-Z]+)([0-9]{4})([0-9]{4})?/";
		$matches = array();

		//can the pattern be found?
		if(preg_match($regexp, $semesterString, $matches)){
			$year = (int) $matches[2];
			$semesters = $this->getSemestersStartingInYear($year);

			if(isset($semesters[$semesterString])){
				return true;
			}
		}

		return false;
	}

	function getSemesterFromSemesterString($semesterString){
		$regexp = "/[a-zA-Z]+([0-9]{4})[0-9]*/";
		$matches = array();

		//find year
		if(preg_match($regexp, $semesterString, $matches)){
			$year = (int) $matches[1];
			$semesters = $this->getSemestersStartingInYear($year);

			if(isset($semesters[$semesterString])){
				return $semesters[$semesterString];
			}
		} else {
			return false;
		}
	}

	function getSemesterMonate($semesterString){
		if(!$this->isValidSemesterString($semesterString)){
			return array();
		}

		$semester = $this->getSemesterFromSemesterString($semesterString);
		return $semester['months'];
	}

	function getZeitraum($semesterString){
		if(!$this->isValidSemesterString($semesterString)){
			return array();
		}

		$semester = $this->getSemesterFromSemesterString($semesterString);
		return array($semester['startdate'], $semester['enddate']);
	}

	function getFollowingSemester($semester, $previous=0){
		if(!is_array($semester)){
			return array();
		}

		$year = $semester['startyear'];

		if($previous == 0){
			$semesters = array_merge($this->getSemestersInYear($year), $this->getSemestersInYear($year+1));
		} else {
			$semesters = array_reverse(array_merge($this->getSemestersInYear($year-1), $this->getSemestersInYear($year)));
		}

		$nextIsTheOne = false;

		foreach($semesters as $foundSemester){
			if($semester['name'] == $foundSemester['name']){
				$nextIsTheOne = true;
			} elseif($nextIsTheOne){
				return $foundSemester;
			}
		}
	}

	function getPreviousSemester($semester){
		return $this->getFollowingSemester($semester, 1);
	}

	function getSemesterAtDate($date){
		$year = (int) substr($date, 0, 4);
		$semesters = $this->getSemestersInYear($year);

		foreach($semesters as $semester){
			if($semester['startdate'] <= $date && $semester['enddate'] >= $date){
				return $semester;
			}
		}
	}

	//-------------------------------- operations on semester string giving semester string -------------------------------------------------

	function getSemesterNameAtDate($datum){
		$semester = $this->getSemesterAtDate($datum);
		return $semester['name'];
	}

	function getSemesterName(){
		$semester = $this->getSemesterAtDate(@date('Y-m-d'));
		return $semester['name'];
	}

	function getPreviousSemesterName(){
		$semester = $this->getSemesterAtDate(@date('Y-m-d'));
		$previousSemester = $this->getPreviousSemester($semester);
		return $previousSemester['name'];
	}

	function getPreviousSemesterNameOfSemester($semesterString){
		if(!$this->isValidSemesterString($semesterString)){
			return '';
		}

		$semester = $this->getPreviousSemester($this->getSemesterFromSemesterString($semesterString));
		return $semester['name'];
	}

	function getFollowingSemesterName(){
		$semester = $this->getSemesterAtDate(@date('Y-m-d'));
		$followingSemester = $this->getFollowingSemester($semester);
		return $followingSemester['name'];
	}

	function getFollowingSemesterNameOfSemester($semesterString){
		if(!$this->isValidSemesterString($semesterString)){
			return '';
		}

		$semester = $this->getFollowingSemester($this->getSemesterFromSemesterString($semesterString));
		return $semester['name'];
	}

	function getShortSemester($semesterString){
		$semester = $this->getSemesterFromSemesterString($semesterString);
		return $semester['prefix'].$semester['startyear'];
	}

	function getWeekday($datum){
		$wochentage = array('So.', 'Mo.', 'Di.', 'Mi.', 'Do.', 'Fr.', 'Sa.');
		$wochentag = $wochentage[@date('w', strtotime($datum))];
		return $wochentag;
	}

	function getMonth($i){
		$months = array('Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli',
				'August', 'September', 'Oktober', 'November', 'Dezember');
		$monthIndex = $i-1;
		$result = isset($months[$monthIndex]) ? $months[$monthIndex] : '';
		return $result;
	}

	function getSemestersFromDates($daten){
		$semesters = array();

		for($i = 0; $i<count($daten); $i++){
			$semester = $this->getSemesterNameAtDate($daten[$i]);

			if($semester != ''){
				$semesters[] = $semester;
			}
		}

		$semesters2 = array_unique($semesters);
		$semesters3 = array();

		foreach($semesters2 as $semester){
			$semesters3[] = $semester;
		}

		return $semesters3;
	}

	/**
	* Überprüft einen Geburtstag auf Festlichkeit wie 50ten etc., und gibt Alter zu diesem Geburtstag zurück
	* Es müssen das Geburtsjahr und das aktuelle Jahr in der Form ('1930','2000') übergeben werden.
	*/
	function checkSignificantBirthdayYear($birthYear, $yearNow){
		$relevantAges = array(30, 40, 50, 60, 70, 75, 80, 85, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114, 115, 116, 117, 118, 119, 120);
		$age = $yearNow - $birthYear;

		$result = false;

		if(in_array($age, $relevantAges)){
			$result = $age;
		}

		return $result;
	}

	function determineSemesterCover($semesterString){
		if(!$this->isValidSemesterString($semesterString)){
			return;
		}

		$file = '';

		if(is_file('custom/semestercover/'.$semesterString. '.jpg')){
			$file = $semesterString.'.jpg';
		} elseif(is_file('custom/semestercover/'.strtolower($semesterString). '.jpg')){
			$file = strtolower($semesterString).'.jpg';
		} elseif(is_file('custom/semestercover/'.$semesterString. '.png')){
			$file = $semesterString.'.png';
		} elseif(is_file('custom/semestercover/'.strtolower($semesterString). '.png')){
			$file = strtolower($semesterString).'.png';
		} elseif(is_file('custom/semestercover/'.$semesterString. '.gif')){
			$file = $semesterString.'.gif';
		} elseif(is_file('custom/semestercover/'.strtolower($semesterString). '.gif')){
			$file = strtolower($semesterString). '.gif';
		}

		return $file;
	}

	function hasSemesterCover($semesterString){
		if(!$this->isValidSemesterString($semesterString)){
			return;
		}

		$file = $this->determineSemesterCover($semesterString);
		$result = $file != '';
		return $result;
	}

	function getSemesterCoverString($semesterString){
		if(!$this->isValidSemesterString($semesterString)){
			return;
		}

		$file = $this->determineSemesterCover($semesterString);

		if($file != ''){
            return '<img src="custom/semestercover/' . $file . '" class="img-fluid mx-auto" alt="Semestercover" />';
		}
	}

	function getSemesterString($semesterString, $enableAbbr = true){
		$semester = $this->getSemesterFromSemesterString($semesterString);

		if(!is_array($semester)){
			return false;
		}

		$ssAbk = 'SS';
		$wsAbk = 'WS';

		if($enableAbbr){
			$ssAbk = "<abbr title=\"Sommersemester\">SS</abbr>"; // \xc2\xa0 is non-breaking space
		}

		if($enableAbbr){
			$wsAbk = "<abbr title=\"Wintersemester\">WS</abbr>"; // \xc2\xa0 is non-breaking space
		}

		$ssRegexp = '/SS[0-9]{4}/';
		$wsRegexp = '/WS[0-9]{8}/';

		$jahrestrenner  = '/';
		$space = '&nbsp;';

		$matches = array();

		//summer semester?
		if(preg_match($ssRegexp, $semester['name'], $matches)){
			return $ssAbk.$space.$semester['startyear'];
		}
		//winter semester?
		elseif(preg_match($wsRegexp, $semester['name'], $matches)){
			return $wsAbk.$space.$semester['startyear'].$jahrestrenner.substr($semester['endyear'], 2, 2);
		} else {
			if($semester['overlapping']){
				return $semester['prefix'].$space.$semester['startyear'].$jahrestrenner.substr($semester['endyear'], 2, 2);
			} else {
				return $semester['prefix'].$space.$semester['startyear'];
			}
		}
	}

    function getSemesterMenu($semesters, $globalsemester): string
    {
		global $libGlobal;

		$retstr = '';

		if(count($semesters) > 1 || (count($semesters) == 1 && ($semesters[0] != $globalsemester))){
            $retstr .= '<div class="card">';
            $retstr .= '<div class="card-body">';
			$retstr .= '<form action="index.php" class="form-inline">';
            $retstr .= '<div class="row">';
            $retstr .= '<div class="col-12">';
			$retstr .= '<input type="hidden" name="pid" value="' . $libGlobal->pid . '"/>';
			$retstr .= '<label for="semester" class="sr-only">Semester</label>';
            $retstr .= '<select id="semester" name="semester" class="form-select" onchange=\'this.form.submit()\'>';

			foreach($semesters as $semester){
				if($semester != '' && $this->isValidSemesterString($semester)){
					$retstr .= '<option value="' .$semester. '"';

					if($semester == $globalsemester){
						$retstr .= ' selected="selected"';
					}

					$retstr .= '>';
					$retstr .=  $this->getSemesterString($semester, false);
					$retstr .= '</option>';
				}
			}

			$retstr .= '</select> ';
            $retstr .= '</div>';
            $retstr .= '</div>';
            $retstr .= '</form>';
			$retstr .= '</div>';
			$retstr .= '</div>';
		}

		return $retstr;
	}

	//-------------------- conversions ------------------------------------------

	function formatDateString($dateTime){
		return substr($dateTime, 8, 2) .'.'. substr($dateTime, 5, 2) .'.'. substr($dateTime, 0, 4);
	}

	function formatTimeString($dateTime){
		$time = substr($dateTime, 11, 5);
		$result = '';

		// no time
		if($time == '00:00'){
			$result .= '';
		} elseif(substr($time, 3, 2) == 00){
			$result .= ' ' .substr($time, 0, 2). 'h s.t.';
		} elseif(substr($time, 3, 2) == 15){
			$result .= ' ' .substr($time, 0, 2). 'h c.t.';
		} else {
			$result .= ' ' .$time. 'h';
		}

		return $result;
	}

	function formatDateTimeString($dateTime){
		$dateString = $this->formatDateString($dateTime);
		$timeString = $this->formatTimeString($dateTime);

		return $dateString. ' ' .$timeString;
	}

	function formatYearString($dateTime){
		return (int) substr($dateTime, 0, 4);
	}

	function formatMonthString($dateTime){
		return (int) substr($dateTime, 5, 2);
	}

	function formatDayString($dateTime){
		return (int) substr($dateTime, 8, 2);
	}

	function formatUtcString($dateTime){
		$year = (int) substr($dateTime, 0, 4);
		$month = (int) substr($dateTime, 5, 2);
		$day = (int) substr($dateTime, 8, 2);
		$hour = (int) substr($dateTime, 11, 2);
		$minute = (int) substr($dateTime, 14, 2);
		$second = (int) substr($dateTime, 17, 2);

		if($hour == 0 && $minute == 0 && $second == 0){
			return str_pad($year, 4, '0', STR_PAD_LEFT). '-' .str_pad($month, 2, '0', STR_PAD_LEFT). '-' .str_pad($day, 2, '0', STR_PAD_LEFT);
		} else {
			$dateTimeObject = new DateTime($dateTime);
			$dateTimeObject->setTimezone(new DateTimeZone('UTC'));
			return $dateTimeObject->format('Y-m-d').'T'.$dateTimeObject->format('H:i:s').'Z';
		}
	}

	function assureMysqlDateTime($dateTime){
		$dateTime = trim($dateTime);

		$regexpDatumComplete					= "/([0-9]{1,2}).([0-9]{1,2}).([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/";
		$regexpDatumWithoutSeconds 				= "/([0-9]{1,2}).([0-9]{1,2}).([0-9]{4}) ([0-9]{1,2}):([0-9]{1,2})/";
		$regexpDatumWithoutMinutes 				= "/([0-9]{1,2}).([0-9]{1,2}).([0-9]{4}) ([0-9]{1,2})/";
		$regexpDatumWithoutHour 				= "/([0-9]{1,2}).([0-9]{1,2}).([0-9]{4})/";

		$regexpMysqlDateTimeComplete			= "/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/";
		$regexpMysqlDateTimeWithoutSeconds		= "/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2})/";
		$regexpMysqlDateTimeWithoutMinutes		= "/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2})/";
		$regexpMysqlDate 						= "/([0-9]{4})-([0-9]{2})-([0-9]{2})/";

		$matchesDatum = array();

		if(preg_match($regexpDatumComplete, $dateTime, $matchesDatum)){
			return $this->zeroFill($matchesDatum[3], 4).'-'.$this->zeroFill($matchesDatum[2]).'-'.$this->zeroFill($matchesDatum[1]).' '.$this->zeroFill($matchesDatum[4]).':'.$this->zeroFill($matchesDatum[5]).':'.$this->zeroFill($matchesDatum[6]);
		} elseif(preg_match($regexpDatumWithoutSeconds, $dateTime, $matchesDatum)){
			return $this->zeroFill($matchesDatum[3], 4).'-'.$this->zeroFill($matchesDatum[2]).'-'.$this->zeroFill($matchesDatum[1]).' '.$this->zeroFill($matchesDatum[4]).':'.$this->zeroFill($matchesDatum[5]).':00';
		} elseif(preg_match($regexpDatumWithoutMinutes, $dateTime, $matchesDatum)){
			return $this->zeroFill($matchesDatum[3], 4).'-'.$this->zeroFill($matchesDatum[2]).'-'.$this->zeroFill($matchesDatum[1]).' '.$this->zeroFill($matchesDatum[4]).':00:00';
		} elseif(preg_match($regexpDatumWithoutHour, $dateTime, $matchesDatum)){
			return $this->zeroFill($matchesDatum[3], 4).'-'.$this->zeroFill($matchesDatum[2]).'-'.$this->zeroFill($matchesDatum[1]).' 00:00:00';
		} elseif(preg_match($regexpMysqlDateTimeComplete, $dateTime)){
			return $dateTime;
		} elseif(preg_match($regexpMysqlDateTimeWithoutSeconds, $dateTime, $matchesDatum)){
			return $this->zeroFill($matchesDatum[1], 4).'-'.$this->zeroFill($matchesDatum[2]).'-'.$this->zeroFill($matchesDatum[3]).' '.$this->zeroFill($matchesDatum[4]).':'.$this->zeroFill($matchesDatum[5]).':00';
		} elseif(preg_match($regexpMysqlDateTimeWithoutMinutes, $dateTime, $matchesDatum)){
			return $this->zeroFill($matchesDatum[1], 4).'-'.$this->zeroFill($matchesDatum[2]).'-'.$this->zeroFill($matchesDatum[3]).' '.$this->zeroFill($matchesDatum[4]).':00:00';
		} elseif(preg_match($regexpMysqlDate, $dateTime, $matchesDatum)){
			return $this->zeroFill($matchesDatum[1], 4).'-'.$this->zeroFill($matchesDatum[2]).'-'.$this->zeroFill($matchesDatum[3]).' 00:00:00';
		} else {
			return '';
		}
	}

	function assureMysqlDate($date){
		$date = trim($date);
		$regexpDatum = "/([0-9]{1,2}).([0-9]{1,2}).([0-9]{4})/";
		$regexpMysqlDate = "/([0-9]{4})-([0-9]{2})-([0-9]{2})/";
		$regexpJahr = "/([0-9]{4})/";

		$matchesDatum = array();

		if(preg_match($regexpDatum, $date, $matchesDatum)){
			return $this->zeroFill($matchesDatum[3], 4).'-'.$this->zeroFill($matchesDatum[2]).'-'.$this->zeroFill($matchesDatum[1]);
		} elseif(preg_match($regexpMysqlDate, $date)){
			return $date;
		} elseif(preg_match($regexpJahr, $date)){
			return $date.'-00-00';
		} else {
			return '';
		}
	}

	function zeroFill($number, $stellen=2){
		return str_pad((int) $number, $stellen, '0', STR_PAD_LEFT);
	}

	function getNumberOfDaysInMonth($year, $month){
		$month = (int) $month;
		return 31-((($month-(($month<8)?1:0))%2)+(($month==2)?((!($year%((!($year%100))?400:4)))?1:2):0));
	}
}
