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
 * @file
 * @abstract Various http constants are collected here. They may or may not be a complete
 * list as I will be focusing on those I need to use for the time being.
 *
 * 
 *
 * @license Apache Version 2.0
 *
 * @author Robert Olendorf
 *
 * @license Apache Version 2.0
 * @package HTTPClasses
 */
class HTTPResponseHeaders {

  /**
   * What partial content range types this server supports
   *
   * @example Accept-Ranges: bytes
   */
  const ACCEPT_RANGES = 'Accept-Ranges';

  /**
   * The age the object has been in a proxy cache in seconds
   *
   * @example Age: 12
   */
  const AGE = 'Age';

  /**
   * Valid actions for a specified resource. To be used for a 405 Method not allowed
   *
   * @example Allow: GET, HEAD
   */
  const ALLOW = 'Allow';

  /**
   * Tells all caching mechanisms from server to client whether they may cache
   * this object. It is measured in seconds
   *
   * @example Cache-Control: max-age=3600
   */
  const CACHE_CONTROL = 'Cache-Control';

  /**
   * Options that are desired for the connection
   *
   * @example Connection: close
   * @link http://tools.ietf.org/html/rfc4229
   */
  const CONNECTION = 'Connection';

  /**
   * The type of encoding used on the data. See HTTP compression.
   *
   * @example Content-Encoding: gzip
   * @link http://en.wikipedia.org/wiki/HTTP_compression
   */
  const CONTENT_ENCODING = 'Content-Encoding';

 /**
   * The language the content is in
  *
   * @example Content-Language: da
   * @link http://www.w3.org/WAI/ER/IG/ert/iso639.htm
   *
   */
  const CONTENT_LANGUAGE = 'Content-Language';

  /**
   * The length of the response body in octets (8-bit bytes)
   *
   * @example Content-Length: 348
   */
  const CONTENT_LENGTH = 'Content-Length';

  /**
   * An alternate location for the returned data
   *
   * @example Content-Location: /index.htm
   */
  const CONTENT_LOCATION = 'Content-Location';

  /**
   * A Base64-encoded binary MD5 sum of the content of the response
   *
   * @example Content-MD5: Q2hlY2sgSW50ZWdyaXR5IQ==
   */
  const CONTENT_MD5 = 'Content-MD5';

  /**
   * An opportunity to raise a "File Download" dialogue box for a known MIME type
   *
   * @example Content-Disposition: attachment; filename=fname.ext
   */
  const CONTENT_DISPOSITION = 'Content-Disposition';

  /**
   * Where in a full body message this partial message belongs
   *
   * @example Content-Range: bytes 21010-47021/47022
   */
  const CONTENT_RANGE = 'Content-Range';

  /**
   * The mime type of this content
   *
   * @example Content-Type: text/html; charset=utf-8
   */
  const CONTENT_TYPE = 'Content-Type';

  /**
   * The date and time that the message was sent
   *
   * @example Date: Tue, 15 Nov 1994 08:12:31 GMT
   */
  const DATE = 'Date';

  /**
   * An identifier for a specific version of a resource, often a message digest
   *
   * @example ETag: "737060cd8c284d8af7ad3082f209582d"
   */
  const ETAG = 'ETag';

  /**
   * Gives the date/time after which the response is considered stale
   *
   * @example Expires: Thu, 01 Dec 1994 16:00:00 GMT
   */
  const EXPIRES = 'Expires';

  /**
   * The last modified date for the requested object, in RFC 2822 format
   *
   * @example Last-Modified: Tue, 15 Nov 1994 12:45:26 GMT
   */
  const LAST_MODIFIED = 'Last-Modified';

  /**
   * Used to express a typed relationship with another resource, where the
   * relation type is defined by RFC 5988
   *
   * @example Link: </feed>; rel="alternate"
   * @link http://tools.ietf.org/html/rfc5988
   */
  const LINK = 'Link';

  /**
   * Used in redirection, or when a new resource has been created.
   *
   * @example Location: http://www.w3.org/pub/WWW/People.html
   */
  const LOCATION = 'Location';

  /**
   * Implementation-specific headers that may have various effects anywhere
   * along the request-response chain.
   *
   * @example Pragma: no-cache
   */
  const PRAGMA = 'Pragma';

  /**
   * Request authentication to access the proxy.
   *
   * @example Proxy-Authenticate: Basic
   */
  const PROXY_AUTHENTICATE = 'Proxy-Authenticate';

  /**
   * Used in redirection, or when a new resource has been created. This refresh
   * redirects after 5 seconds. This is a proprietary, non-standard header
   * extension introduced by Netscape and supported by most web browsers.
   *
   * @example Refresh: 5; url=http://www.w3.org/pub/WWW/People.htm
   */
  const REFRESH = 'Refresh';

  /**
   * If an entity is temporarily unavailable, this instructs the client to try
   * again after a specified period of time.
   *
   * @example Retry-After: 120
   */
  const RETRY_AFTER = 'Retry-After';

  /**
   * A name for the server
   *
   * @example Server: Apache/1.3.27 (Unix) (Red-Hat/Linux)
   */
  const SERVER = 'Server';

  /**
   * an HTTP cookie
   *
   * @example Set-Cookie: UserID=JohnDoe; Max-Age=3600; Version=1
   */
  const SET_COOKIE = 'Set-Cookie';

  /**
   * A HSTS Policy informing the HTTP client how long to cache the HTTPS only
   * policy and whether this applies to subdomains.
   *
   * @example Strict-Transport-Security: max-age=16070400; includeSubDomains
   */
  const STRICT_TRANSPORT_SECURITY= 'Strict-Transport-Security';

  /**
   * The Trailer general field value indicates that the given set of header
   * fields is present in the trailer of a message encoded with chunked
   * transfer-coding.
   *
   * @example Trailer: Max-Forwards
   */
  const TRAILER = 'Trailer';

  /**
   * The form of encoding used to safely transfer the entity to the user.
   * Currently defined methods are: chunked, compress, deflate, gzip,
   * identity.
   *
   * @example Transfer-Encoding: chunked
   */
  const TRANSFER_ENCODING = 'Transfer-Encoding';

  /**
   * Tells downstream proxies how to match future request headers to
   * decide whether the cached response can be used rather than
   * requesting a fresh one from the origin server.
   *
   * @example Vary: *
   */
  const VARY = 'Vary';

  /**
   * Informs the client of proxies through which the response was sent.
   *
   * @example Via: 1.0 fred, 1.1 nowhere.com (Apache/1.1)
   */
  const VIA = 'Via';

  /**
   * A general warning about possible problems with the entity body.
   *
   * @example Warning: 199 Miscellaneous warning
   */
  const WARNING = 'Warning';

  /**
   * Indicates the authentication scheme that should be used to access the
   * requested entity.
   *
   * @example WWW-Authenticate: Basic
   */
  const WWW_AUTHENTICATE = 'WWW-Authenticate';
}

?>
