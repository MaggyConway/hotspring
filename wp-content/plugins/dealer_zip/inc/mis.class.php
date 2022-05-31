<?php

/*
 * MIS class
 * This class is for managing posting data to MIS lead scoring service.
 */

class MIS {
	protected $endPoint;
	protected $fieldMap;
	
	/* Create object and set the endpoint URL the data will be posted to */
	function __construct($endPoint,$fieldMap) {
		assert('filter_var($endPoint, FILTER_VALIDATE_URL)');
		
		$this->endPoint = $endPoint;
		$this->fieldMap = $fieldMap;
		
		assert('$this->isSane()');
	}
	
	/* Checks the internal state of the object.  
	 * Returns false if the member variables are in an invalid state.
	 * This should be run at the end of all public methods to ensure the object is Sane. 
	 */
	protected function isSane() {
		$isSane = true;
		if (filter_var($this->endPoint, FILTER_VALIDATE_URL) === false) {
			$isSane = false;
		}
		if (!is_array($this->fieldMap)) {
			$isSane = false;
		}
		return $isSane;
	}
	
	/*
	 * Validates a field map.
	 * This is part of sanity check and parameter validation so it doesn't need its own. 
	 */
	protected function validateFieldMap($fieldMap) {
		$isValid = true;
		
		return $isValid;
	}
	
	/* Setter for end point URL */
	public function setEndPoint($endPoint) {
		assert('filter_var($endPoint, FILTER_VALIDATE_URL)');
		
		$this->endPoint = $endPoint;
		
		assert('$this->isSane()');
	}

	/* Getter for end point URL */
	public function getEndPoint() {
		$endPoint =$this->endPoint;
		
		assert('$this->isSane()');
		assert('filter_var($endPoint, FILTER_VALIDATE_URL)');
		return $endPoint;
	}
	
	/* Take submission data from lead form and send POST to MIS */
	public function sendLead($formData) {
		$misData = '';
		$paramCount = 0;
		foreach($this->fieldMap as $webformKey => $misKey) {
			if (isset($formData[$webformKey]['#value'])) {
				if(is_array($formData[$webformKey]['#value'])){
					foreach ($formData[$webformKey]['#value'] as $value) {
						$misData .= $misKey.'='.urlencode($value).'&';
						$paramCount++;
					}
				} else {
					$misData .= $misKey.'='.urlencode($formData[$webformKey]['#value']).'&';
					$paramCount++;
				}
					
			}
		}
		rtrim($misData, '&');
		
		$ch = curl_init($this->endPoint); // open connection
		curl_setopt($ch, CURLOPT_POST, $paramCount);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $misData);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		
		$result = curl_exec($ch);
		if ($result) {
			watchdog("watkins_mis", "Successfully sent lead to MIS.",array(),WATCHDOG_INFO);
		} else {
			watchdog("watkins_mis", "Failed to send lead to MIS.Curl error: ".curl_error($ch),array(),WATCHDOG_ERROR);
		}
		return $result;
	}
	
}