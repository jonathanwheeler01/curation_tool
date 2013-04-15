<?php
/* 
 *    This file is part of simple_http.

 *    simple_http is free software: you can redistribute it and/or modify
 *    it under the terms of the Apache License, Version 2.0 (See License at the
 *    top of the directory).

 *    simple_http is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

 *    You should have received a copy of the Apache License, Version 2.0
 *    along with simple_http.  If not, see <http://www.apache.org/licenses/LICENSE-2.0.html>.
 */
/**
 * Description of SimpleHTTPRequest
 *
 * @author Rob Olendorf
 */
require_once 'SimpleHTTP.inc';

class SimpleHTTPRequest {
  protected $protocol;
  protected $host;
  protected $path;
//  protected $data;
  protected $method;
  protected $port;
  protected $headers;
  protected $postData;
  protected $queryData;
  protected $responseHeaders;
  protected $response;
  protected $responseProtocol;
  protected $reponseStatus;
  
  // Only handles http and https for now
  public static $URL_REGEX = '/^(?:(http|https):\/\/)?([\w\-_\.]+)+(?:\:?([0-9]*))\/?([-_%\/a-zA-z0-9\.]*)\??([-_%=&a-zA-Z0-9#\+]*)/';
  public static $RESPONSE_REGEX = '/^(HTTP|HTTPS|FTP|FPTS)\/[0-9]+.[0-9] ([a-zA-Z0-9 ]+)/';
  
  /**
   * Sets default protocol to http, default port to 80, default method to GET
   * and initialize the headers array, queryData and postData array.
   */
  public function __construct($url = '') {
    $this->protocol = 'http';
    $this->set_port(80);
    $this->set_method('GET');
    $this->headers = array();
    $this->postData = array();
    $this->queryData = array();
    $this->path = '/';
    if($url) {
      $this->set_url($url);
    }
  }
  
  /**
   * Sets the URL. If an protocol is specified that will replace the default
   * http protocol. If an alternative port is specified that relaces teh default
   * port of 80.
   * @param type $url
   * 
   * @return mixed Returns the object= if the url was parsed, false otherwise.
   */
  public function set_url($url) {
    if(preg_match(self::$URL_REGEX, $url, $urlData)) {                              // Parse the url
      // Only set the protocol and port if they are specified in the URL.
      if($urlData[1]){                                                            // Protocol
        $this->set_protocol($urlData[1]);
      }
      $this->host = $urlData[2];                                                  // host url

      if($urlData[3]){                                                            // Port This will override any automatic port settings
        $this->set_port($urlData[3]);
      }

      if($urlData[4]) {                                                           // Path after the host designation
        $this->path .= $urlData[4];
      }

      if(!(empty($urlData[5]))) {                                                 // Query string
        $this->add_query_data($this->parse_query_string($urlData[5]));
      }
      return $this;
    }
    else {
      return false;
    }
  }

  /**
   * Parses a query string from a URL to an associative array.
   *
   * @param string $queryString Query string field1=val1&field2=val2
   * @return array Associative array (field1=>val1, field2=>val2)
   */
  public function parse_query_string($queryString) {
    $output = array();
    $pairs = explode('&', $queryString);
    foreach($pairs as $pair) {
      $item = explode('=', $pair);
      $output[trim($item[0])] = trim($item[1]);
    }
    return $output;
  }
  
  /**
   * Returns the url sans any protocol or port.
   * 
   * @return string 
   */
  public function get_url() {
    return $this->host;
  }
  
  /**
   * Set the protocol for the request. Default is http.
   * 
   * @param string $protocol 
   * @return SimpleHTTPRequest
   */
  public function set_protocol($protocol) {
    $this->protocol = $protocol;
    if($this->protocol == 'https') {
      $this->set_port(443);
    }
    return $this;
  }
  
  /**
   * Returns the request protocol.
   * 
   * @return string
   */
  public function get_protocol() {
    return $this->protocol;
  }
  
  /**
   * Set the port for the request. Default is 80.
   * 
   * @param int $port 
   * 
   * @return SimpleHTTPRequest
   */
  public function set_port($port) {
    $this->port = $port;
    return $this;
  }
  
  /**
   * Get the current port setting.
   * 
   * @return int
   */
  public function get_port() {
    return $this->port;
  }

  public function get_path() {
    return $this->path;
  }
  
  /**
   * Sets the headers for the request. Previously defined headers will be
   * overwritten. New headers will be added to the array.
   * 
   * @param array $headers An associative array of the headers in the form of
   * array('HeaderName1' => 'header value1', 'HeaderName2' => 'header value 2');
   * 
   * @return SimpleHTTPRequest
   */
  public function add_headers($headers) {
    $this->headers = array_merge(
            array_diff_key($this->headers, $headers),
            $headers
            );
    return $this;
  }
  
  /**
   * Clears all headers.
   * @return SimpleHTTPRequest
   */
  public function clear_headers() {
    $this->headers = array();
    return $this;
  }
  
  /**
   * Returns an associative array of the currently set headers.
   * 
   * @return array 
   */
  public function get_headers() {
    return $this->headers;
  }

  /**
   * Sets the http method to be used for the request.
   *
   * @param string $method
   * @return SimpleHTTPRequest
   */
  public function set_method($method) {
    $this->method = strtoupper($method);
    return $this;
  }

  /**
   * Gets the currently set HTTP Method
   * @return string
   */
  public function get_method() {
    return $this->method;
  }

  /**
   * Add post data. Any previously defined fields are overwritten, new fields
   * are added to the data if the data is in array form and intended to be
   * application/x-www-form-urlencoded. Otherwise the data is concatenated to any
   * prexisting string.
   *
   * @param array $postData Associative array with the field names as keys
   * and the values as values.
   * 
   * @return mixed Returns the object if successful, false otherwise.
   */
  public function add_post_data($postData) {
    // Make sure we are adding similar types of post data. It should either be
    // an array or a string, but not both. If nothing is added yet, were fine.
    if(is_array($postData) && (is_array($this->postData) || empty($this->postData))) {
      $this->postData = array_merge(
              array_diff_key($this->postData, $postData),
              $postData
              );
      $this->add_headers(array(HTTPRequestHeaders::CONTENT_TYPE => 'application/x-www-form-urlencoded'));
      //return true;
      return $this;
    }
    else if( !is_array($postData) && (!is_array($this->postData) || empty($this->postData))) {
      if(empty($this->postData)) {
        $this->postData = '';
      }
      $this->postData .= $postData;
      return $this;
    }
    else {
      return false;
    }
  }

  /**
   * Clear all current post data.
   * @return SimpleHTTPRequest
   */
  public function clear_post_data() {
    $this->postData = array();
    return $this;
  }

  /**
   * Get the currently defined field data.
   *
   * @return array Associative array with keys as field names and values as values.
   */
  public function get_post_data() {
    return $this->postData;
  }

  /**
   * Returns a suitably formatted post string with the fields and values
   * urlencoded.
   *
   * @return string formatted query data in the form of field=value&field=value
   */
  public function get_encoded_post_data() {
    return $this->format_data($this->postData);
  }

  /**
   * Add to the query data. Any previously defined fields are overwritten, new
   * fields are added to the data.
   *
   * @param array $queryData Associative array with the field names as keys
   * and the values as values.
   * @return SimpleHTTPRequest
   */
  public function add_query_data($queryData) {
    if(is_array($this->queryData)){
      $this->queryData = array_merge(
              array_diff_key($this->queryData, $queryData),
              $queryData
              );
    }
    else {
      $this->queryData = $queryData;
    }
    return $this;
  }

  /**
   * Clear any query data.
   * @return SimpleHTTPRequest
   */
  public function clear_query_data() {
    $this->queryData = array();
    return $this;
  }

  /**
   * Get the current query data.
   *
   * @return array The query data with keys as field names and values as values
   */
  public function get_query_data() {
    return $this->queryData;
  }

  /**
   * Returns a suitably formatted query string with the fields and values
   * urlencoded.
   *
   * @return string formatted query data in the form of field=value&field=value
   */
  public function get_encoded_query_data() {
    return $this->format_data($this->queryData);
  }

  /**
   * Helper function.
   *
   * @param array $data Data to be encoded into a field=value&field=value string
   * @return string
   */
  private function format_data($data) {
    $output = '';
    foreach($data as $field => $value) {
      $output.=urlencode($field).'='.urlencode($value).'&';
    }

    return substr($output, 0, -1);

  }

  /**
   * Sends the HTTP request. Returns true if the request was made, false otherwise.
   *
   * @return boolean
   */
  public function send_request($test = FALSE) {
//    Don't allow empty POST requests.
    if($this->method == strtoupper('POST') && empty ($this->postData)) {
      $this->method = 'GET';
    }

    $ssl = '';
    if($this->protocol ==' https') {
      $ssl = 'ssl://';
    }
    
    $this->process_query_data();
    $this->process_post_data();
    $this->ensure_headers();
    
    // Generate the output for the request.
    $output = $this->method.' '.$this->path.$this->queryData." HTTP/1.1\r\n".
              $this->get_formatted_headers().
            "\r\n\r\n";

    // Add any post data.
    if($this->postData) {
        $output .= $this->postData;
    }

    if(!$test){
      $socket = fsockopen($ssl.$this->host, $this->port);                              // open the socket
      if($socket) {                                                               // check the socket opened
        fputs($socket, $output);                                                  // send the request

        // Get the response
        $response = '';
        while(!feof($socket)) {
          $response .= fgets($socket, 256);
        }
        fclose($socket);
        $this->process_response($response);                                       // process the response
        return true;
      }
      else {
        return false;                                                             // failed to open the socket
      }
    }
  }
  
  /**
   * Process the query data, creating a string 
   * if the data is in an array.
   */
  private function process_query_data() {

    // Get the query data if it exisits.
    $query = '';
    if(!(empty ($this->queryData))) {
      $this->queryData = '?'.$this->get_encoded_query_data();
    }
    else {
      $this->queryData = '';
    }
  }

  /**
   * Process the post data into a string suitable for application/x-url-form-encoding
   */
  private function process_post_data() {    
    // Get the length of the post data.
    if($this->postData) {
      if(is_array($this->postData)) {
        $this->postData = $this->get_encoded_post_data();
      }
    }
  }
  
  /**
   * Ensure that any required headers are set. Also defines any strongly encouraged
   * headers for which we can define default values. Only headers not previously
   * defined are added.
   */
  private function ensure_headers() {
    // Set up the headers that are required, or strongly encouraged and able to
    // generate default values for.
    $required = array(
      HTTPRequestHeaders::HOST => $this->host,
      HTTPRequestHeaders::CONNECTION => 'close',
    );
    
    if(is_array($this->postData)) {
      $this->postData = $this->get_encoded_post_data();
      $required[HTTPRequestHeaders::CONTENT_TYPE] = 'application/x-www-form-urlencoded';
    }
    
    if(isset($this->postData) && !empty($this->postData)) {
      $required[HTTPRequestHeaders::CONTENT_LENGTH] = strlen($this->postData);
      $required[HTTPRequestHeaders::CONTENT_MD5] = base64_encode(md5($this->postData));
    }
    else {
      $required[HTTPRequestHeaders::CONTENT_LENGTH] = 0;
    }
    
    // Only include the headers that have not been define previously.
    $this->headers = array_merge($this->headers, array_diff_key($required, $this->headers));
  }

  /**
   * Formats the headers that they are formatted for an http request.
   *
   * @return string The headers formatted for an HTTP request
   */
  public function get_formatted_headers() {
    $formattedHeaders = '';
    foreach($this->headers as $header => $value) {
      $formattedHeaders .= $header.": ".$value."\r\n";
    }
    return $formattedHeaders;
  }

  /**
   * Processes the response to an HTTP request, parsing out the protocol,
   * response status code, headers and response body.
   *
   * @param string $response
   */
  protected function process_response($response) {
    $response = explode("\r\n\r\n", $response);
    $this->response = $response[1];
    
    $headers = explode("\r\n", $response[0]);
    preg_match(self::$RESPONSE_REGEX, $headers[0], $status);
    $this->responseProtocol = $status[1];
    $this->reponseStatus = (integer)$status[2];
    $headers = array_slice($headers, 1);
    $this->responseHeaders = array();
    foreach($headers as $header) {
      $header = explode(':', $header);
      $this->responseHeaders[trim($header[0])] = trim($header[1]);
    }
  }

  /**
   * Gets the response from an http request
   * @return string
   */
  public function get_response () {
    return $this->response;
  }

  /**
   * Returns the response headers sent with the response to a request.
   * @return array
   */
  public function get_response_headers() {
    return $this->responseHeaders;
  }

  /**
   * Returns the protocol of the response.
   *
   * @return string
   */
  public function get_response_protocol() {
    return $this->responseProtocol;
  }

  /**
   * Returns the HTTP Response code sent with the request.
   *
   * @return integer
   */
  public function get_response_status() {
    return $this->reponseStatus;
  }
}

?>
