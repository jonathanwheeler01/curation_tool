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
 * Just a bunch of constants to help in choosing HTTP Request Headers.
 * The information is pretty much all from 
 * {@link http://en.wikipedia.org/wiki/HTTP_compression Wikipedia}
 * 
 * @package SimpleHTTP
 * @author
 */
class HTTPRequestHeaders {
  /**
   * Content-Types that are acceptable
   * @example text/plain
   */
  const ACCEPT = 'Accept';
  
  /**
   * Character sets that are acceptable
   * @example utf-8
   */
  const ACCEPT_CHARSET = 'Accept-Charset';

  /**
   * Acceptable encodings. 
   * @example gzip, deflate
   * @see http://en.wikipedia.org/wiki/HTTP_compression
   */
  const ACCEPT_ENCODING = 'Accept_Encoding';
  
  /**
   * Acceptable languages for response
   * 
   * @example en-US
   */
  const ACCEPT_LANGUAGE = 'Accept-Languaguage';
  
  /**
   * Authentication credentials for HTTP authentication
   * @example Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==
   */
  const AUTHORIZATION = 'Authorization';
  
  /**
   * Used to specify directives that MUST be obeyed by all caching mechanisms 
   * along the request/response chain.
   * 
   * @example no-cache
   */
  const CACHE_CONTROL = 'Cache-Control';
  
  /**
   * What type of connection the user-agent would prefer
   * 
   * @example close
   */
  const CONNECTION = 'Connection';
  
  /**
   * an HTTP cookie previously sent by the server with Set-Cookie (below)
   * @see $SET_COOKIE
   * @example $Version=1; Skin=new;
   * 
   */
  const COOKIE = 'Cookie';
  
  /**
   * The length of the request body in octets (8-bit bytes)
   * @example 5428
   */
  const CONTENT_LENGTH = 'Content-Length';
  
  /**
   * A Base64-encoded binary MD5 sum of the content of the request body
   * @example Q2hlY2sgSW50ZWdyaXR5IQ==
   */
  const CONTENT_MD5 = 'Content-MD5';
  
  /**
   * The mime type of the body of the request (used with POST and PUT requests)
   * @example application/x-www-form-urlencoded
   */
  const CONTENT_TYPE= 'Content-Type';
  
  /**
   * The date and time that the message was sent
   * @example Tue, 15 Nov 1994 08:12:31 GMT
   */
  const DATE = 'Date';
  
  /**
   * Indicates that particular server behaviors are required by the client
   * @example Expect: 100-continue
   */
  const EXPECT = 'Expect';
  
  /**
   * The email address of the user making the request
   * @example user@example.com
   */
  const FROM = 'From'; 
  
  /**
   * The domain name of the server (for virtual hosting), mandatory since HTTP/1.1
   * @example en.wikipedia.org
   */
  const HOST = 'Host'; 
  
  /**
   * Only perform the action if the client supplied entity matches the same 
   * entity on the server. This is mainly for methods like PUT to only update a 
   * resource if it has not been modified since the user last updated it.
   * @example "737060cd8c284d8af7ad3082f209582d"
   */
  const IF_MATCH = 'If-Match'; 
  
  /**
   * Allows a 304 Not Modified to be returned if content is unchanged
   * @example Sat, 29 Oct 1994 19:43:31 GMT
   */
  const IF_MODIFIED_SINCE = 'If-Modified-Since'; 
  
  /**
   * Allows a 304 Not Modified to be returned if content is unchanged, see 
   * {@link http://en.wikipedia.org/wiki/HTTP_ETag HTTP ETag}
   */
  const IF_MATCH_NONE= 'If-Match-None'; 
  
  /**
   * If the entity is unchanged, send me the part(s) that I am missing; 
   * otherwise, send me the entire new entity\
   * @example "737060cd8c284d8af7ad3082f209582d"
   */
  const IF_RANGE= 'If-Range'; 
  
  /**
   * Only send the response if the entity has not been modified since a 
   * specific time.
   * @example Sat, 29 Oct 1994 19:43:31 GMT
   */
  const IF_UNMODIFIED_SINCE= 'If-Unmodified-Since'; 
  
  /**
   * Limit the number of times the message can be forwarded through proxies or gateways.
   * @example 10
   */
  const MAX_FORWARDS = 'Max-Forwards'; 
  
  /**
   * Implementation-specific headers that may have various effects anywhere 
   * along the request-response chain.
   * @example no-cache
   */
  const PRAGMA = 'Pragma'; 
  
  /**
   * Authorization credentials for connecting to a proxy.
   * @example Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==
   */
  const PROXY_AUTHORIZATION = 'Proxy-Authorization'; 
  
  /**
   * Request only part of an entity. Bytes are numbered from 0.
   * @example bytes=500-999
   */
  const RANGE= 'Range'; 
  
  /**
   * This is the address of the previous web page from which a link to the 
   * currently requested page was followed. (The word “referrer” is misspelled 
   * in the RFC as well as in most implementations.)
   * @example http://en.wikipedia.org/wiki/Main_Page
   */
  const REFERER= 'Referer'; 
  
  /**
   * The transfer encodings the user agent is willing to accept: the same values
   *  as for the response header Transfer-Encoding can be used, plus the 
   * "trailers" value (related to the "chunked" transfer method) to notify the 
   * server it accepts to receive additional headers (the trailers) after the 
   * last, zero-sized, chunk.
   * @example trailers, deflate
   */
  const TE = 'TE'; 
  
  /**
   * Ask the server to upgrade to another protocol.
   * @example HTTP/2.0, SHTTP/1.3, IRC/6.9, RTA/x11
   */
  const UPGRADE = 'Upgrade'; 
  
  /**
   * The user agent string of the user agent
   * @example Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)
   */
  const USER_AGENT = 'User-Agent'; 
  
  /**
   * Informs the server of proxies through which the request was sent.
   * @example 1.0 fred, 1.1 nowhere.com (Apache/1.1)
   */
  const VIA = 'Via'; 
  
  /**
   * A general warning about possible problems with the entity body.
   * @example 199 Miscellaneous warning
   */
  const WARNING = 'Warning'; 
}
  ?>