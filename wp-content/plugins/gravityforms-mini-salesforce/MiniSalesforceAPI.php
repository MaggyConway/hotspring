<?php
/**
 * MiniSalesforceAPI
 *
 * Simple wrapper for sending data to Salesforce
 *
 */

class MiniSalesforceAPI
{

	public $host = 'webto.salesforce.com';
	public $servlet = 'WebToCase';
	//????
	public $v = '1.0';


	/**
	 * @var array An array of method specific parameters to be sent through with the API call.
	 */
	private $params = array();
  private $response = '';
  private $responseInfo = array();

	private function setParams($params){
		$this->params = $params;
	}

  private function setResponse($response){
		$this->response = $response;
	}
  private function setResponseInfo($info){
		$this->responseInfo = $info;
	}
  public function getResponseInfo(){
		return $this->responseInfo;
	}

  public function addParams($name, $value){
		$this->params[$name] = $value;
	}

	public function getPostData(){
	  $params = $this->params;
    unset($params['custom_endpoint']);
		return http_build_query($params);
	}

  private function getUrl(){
	  // Allows to set custom endpoint.
	  if (isset($this->params['custom_endpoint'])) {
	    return $this->params['custom_endpoint'];
    }
    return sprintf('https://%s/servlet/servlet.%s?encoding=UTF-8', $this->host, $this->servlet);
  }

  public function toLog( $log_msg  ) {
    $log_file_data = './log_time_' . date("Y-m-d-H-i-s") . '.log';
    // if you don't add `FILE_APPEND`, the file will be erased each time you add a log
    if(is_string($log_msg)){
      file_put_contents($log_file_data, $log_msg . "\n", FILE_APPEND);
    } else {
      file_put_contents($log_file_data, print_r( $log_msg, 1 ) . "\n", FILE_APPEND);
    }
  }

	private function makeCall(){
    $start_time = microtime(true);
		$url  = $this->getUrl();
		$post = $this->getPostData();

		// Here is where we check for cURL. If we don't find it we make a fopen call...
		if ( function_exists('curl_exec') ) {

			$curl = curl_init();
			curl_setopt( $curl, CURLOPT_URL, $url );
			curl_setopt( $curl, CURLOPT_POST, TRUE );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $post );
			// curl_setopt( $curl, CURLOPT_HEADER, false );
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt( $curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded') );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, FALSE );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );

			$response = curl_exec($curl);

      $this->setResponse($response);
      $responseInfo = curl_getinfo($curl);
      //$this->toLog( $responseInfo );
      // $responseInfo = array(
      //   'status' => curl_getinfo($curl),
      //   'code' => curl_getinfo($curl,CURLINFO_HTTP_CODE),
      //   'total_time' => curl_getinfo($curl,CURLINFO_TOTAL_TIME),
      //   'content_type' => curl_getinfo($curl,CURLINFO_CONTENT_TYPE),
      // );

      $this->setResponseInfo($responseInfo);
			curl_close($curl);

		}	else {
  			$context = stream_context_create(array('http'=>array('method'=>'POST','content'=>$post)));
  			$fp = @fopen($url, 'rb', FALSE, $context);
  			$response = @stream_get_contents($fp);
  			@fclose($fp);
        $this->setResponse($response);
  			if ($response == '') $response = sprintf("The server returned no useable data. This likely points to a NULL result. Try installing php-curl for better error handling.\n");
  		}

    $this->setResponse($response);
    $end_time = microtime(true);
    //$this->toLog('makeCall: ' . ( ( $end_time - $start_time ) * 1000 ) / 60 );
		return $response;
	}

	public function send($params = NULL){
		if ($params !== NULL) {
		  $this->setParams($params);
    }
  	return $this->makeCall();
	}

}
