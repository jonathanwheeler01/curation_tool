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
 *
 * 
 * Since the PECL HTTP classes arent supported on my hosting service, I'm writing
 * this set of classes to mimic what I need. My apologies to what looks like a 
 * great set of classes. Its really a pretty simple bean pattern, with a send()
 * method that outputs the headers and the response.
 *
 * @author Robert Olendorf
 *
 * @license Apache Version 2.0
 * @package HTTPClasses
 * 
 * 
 */
require_once 'SimpleHTTP.inc';

class SimpleHTTPResponse {
  
  /**
   * The data to be sent back with the response
   * 
   * @var HTTP Response
   */
  protected $response = '';
  
  /**
   *
   * @var HTTP Heaaders, most of the headers to be send a long with a request.
   */
  private $headers;
  private $status;


  /**
   * Constructs a SimpleHTTPResponse.
   */
  public function __construct() {
    $this->headers = array();
    $this->set_content_type();
    $this->set_status();
    $this->set_cache();
    $this->set_header('Date', date('r', time()));
    if(isset($_SERVER['SERVER_NAME'])) {
      $this->set_header('Host', $_SERVER['SERVER_NAME']);
    }
  }

  /**
   * Set the content to be returned
   *
   * @param <String> $response
   *    The content to be sent back to the requestor
   * @return SimpleHTTPResponse
   */
  public function set_response($response) {
    $this->set_header('Content-Length', strlen($response));
    $this->set_header('Content-MD5', base64_encode(md5($response)));
    $this->response = $response;
    return $this;
  }

  /**
   *
   * @return <String> Get the currently set response
   */
  public function get_response() {
    return $this->response;
  }

  /**
   * Set a header and its value. The header should be one of the W3C specified
   * headers http://en.wikipedia.org/wiki/List_of_HTTP_headers
   *
   *
   * @param <String> $header
   *    The name of the header
   * @param <String> $value
   * 
   *    The value of the header
   * @return SimpleHTTPResponse
   */
  public function set_header($header, $value) {
    $this->headers[$header] = $value;
    return $this;
  }

  /**
   *
   * @return <array>  An associative array of the headers, keyed on the header name.
   */
  public function get_headers() {
    return $this->headers;
  }

  /**
   * Get a specific header.
   *
   * @param <String> $header
   *    The name of the header to get
   * @return <String>
   *    The value of the header is returned as a string.
   */
  public function get_header($header) {
    return $this->headers[$header];
  }

  /**
   * Set the content type header. The default value is
   * 'text/plain; charset=utf-8'
   *
   * @param <String> $contentType
   *    The mimetype of the file.
   * @return SimpleHTTPResponse
   */
  public function set_content_type($contentType = 'text/html; charset=utf-8') {
    $this->headers['Content-type'] = $contentType;
    return $this;
  }

  /**
   *
   * @return <String>  Return the response content type.
   */
  public function get_content_type() {
    return $this->headers['Content-type'];
  }

  /**
   * Set the response status. The default value being HTTP/1.0 200 OK.
   * All status codes are outlined in HTTPConstants.
   *
   * @param <String> $status
   *    The desired status code. Idealy pulled from HTTPConstants
   * @return SimpleHTTPResponse
   */
  public function set_status($status = '200 OK') {
    $this->status = $status;
    return $this;
  }

  /**
   * @return <String>  currently set http status code
   */
  public function get_status() {
   return $this->status;
  }

  /**
   * Sets the Pragma cache header. Default is no-cache.
   * @param <String> $cache
   *
   * @return SimpleHTTPResponse
   */
  public function set_cache($cache = 'no-cache') {
    $this->headers['Pragma'] = $cache;
    return $this;
  }

  /**
   * Get the currently set cache option
   *
   * @return <String> Return the cache setting
   */
  public function get_cache() {
    return $this->headers['Pragma'];
  }

  /**
   * Send the headers followed by the currently set response.
   */
  public function  send($test = FALSE) {
    if(isset($_SERVER['SERVER_PROTOCOL'])) {
      $protocol = $_SERVER['SERVER_PROTOCOL'];
      header($protocol.' '.$this->status, TRUE, $status_code);
    }
    $status_code = explode(' ', $this->status);
    $status_code = (integer)$status_code[0];
    
    if($test == FALSE) {
      foreach($this->headers as $header => $value) {
        header($header.': '.$value);
      }
    }
    echo $this->response;
  }
}

?>
