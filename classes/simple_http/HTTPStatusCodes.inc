<?php
class HTTPStatusCodes {
  /**
 * Just a bunch of constants to help in choosing an HTTP Response code.
 * The information is pretty much all from 
 * {@link http://en.wikipedia.org/wiki/HTTP_compression Wikipedia}
 * 
 * @package SimpleHTTP
 * @author
 */
  
  /**
   * The client SHOULD continue with its request. This interim response is used
   * to inform the client that the initial part of the request has been received
   * and has not yet been rejected by the server. The client SHOULD continue
   * by sending the remainder of the request or, if the request has already
   * been completed, ignore this response.
   */
  const STATUS_CONTINUE = '100 Continue';

  /**
   * The request has succeeded. The information returned with the response is dependent on the method used in the request\
   *
   * @example GET an entity corresponding to the requested resource is sent in the response;\
   * @example HEAD the entity-header fields corresponding to the requested resource are sent in the response without any message-body;\
   * @example POST an entity describing or containing the result of the action
   * @example TRACE an entity containing the request message as received by the end server.
   */
  const STATUS_OK = '200 OK';

  /**
   * The request has been fulfilled and resulted in a new resource being
   * created. The newly created resource can be referenced by the URI(s)
   * returned in the entity of the response, with the most specific URI for the
   * resource given by a Location header field.
   *
   * A 201 response MAY contain an ETag response header field indicating the
   * current value of the entity tag for the requested variant just created.
   */
  const STATUS_CREATED = '201 Created';
    
  /**
   * The request has been accepted for processing, but the processing has not 
   * been completed. The request might or might not eventually be acted upon, 
   * as it might be disallowed when processing actually takes place. There is 
   * no facility for re-sending a status code from an asynchronous operation 
   * such as this.
   */
  const STATUS_ACCEPTED = '202 Accepted';
  
  /**
   * The returned metainformation in the entity-header is not the definitive 
   * set as available from the origin server, but is gathered from a local or 
   * a third-party copy. The set presented MAY be a subset or superset of the 
   * original version. For example, including local annotation information 
   * about the resource might result in a superset of the metainformation 
   * known by the origin server. Use of this response code is not required 
   * and is only appropriate when the response would otherwise be 200 (OK)
   */
  const STATUS_NON_AUTHORITATIVE_INFORMATION = '203 Non-Authoritative Information';

  /**
   * The server has fulfilled the request but does not need to return an
   * entity-body, and might want to return updated metainformation. The
   * response MAY include new or updated metainformation in the form of
   * entity-headers, which if present SHOULD be associated with the requested
   * variant.
   */
  const STATUS_NO_CONTENT = '204 No Content';

  /**
   * The server has fulfilled the request and the user agent SHOULD reset the
   * document view which caused the request to be sent. This response is
   * primarily intended to allow input for actions to take place via user input,
   * followed by a clearing of the form in which the input is given so that
   * the user can easily initiate another input action. The response MUST NOT
   * include an entity.
   */
  const STATUS_RESET_CONTENT = '205 Reset Content';
  
  /**
   * The server has fulfilled the partial GET request for the resource. The 
   * request MUST have included a Range header field (section 14.35) indicating 
   * the desired range, and MAY have included an If-Range header field 
   * (section 14.27) to make the request conditional.
   */
  const STATUS_PARTIAL_CONTENT = '206 Partial Content';

  /**
   * The requested resource corresponds to any one of a set of representations,
   * each with its own specific location, and agent- driven negotiation
   * information (section 12) is being provided so that the user (or user agent)
   * can select a preferred representation and redirect its request to that
   * location.
   */
  const STATUS_MULTIPLE_CHOICES = '300 Mulitple Choices';

  /**
   * The requested resource has been assigned a new permanent URI and any
   * future references to this resource SHOULD use one of the returned URIs.
   * Clients with link editing capabilities ought to automatically re-link
   * references to the Request-URI to one or more of the new references
   * returned by the server, where possible. This response is cacheable unless
   * indicated otherwise.
   */
  const STATUS_MOVED_PERMANENTLY = '301 Moved Permanently';

  /**
   * The requested resource resides temporarily under a different URI. Since
   * the redirection might be altered on occasion, the client SHOULD continue
   * to use the Request-URI for future requests. This response is only cacheable
   * if indicated by a Cache-Control or Expires header field.
   */
  const STATUS_FOUND = '302 Found';

  /**
   * The response to the request can be found under a different URI and SHOULD
   * be retrieved using a GET method on that resource. This method exists
   * primarily to allow the output of a POST-activated script to redirect
   * the user agent to a selected resource. The new URI is not a substitute
   * reference for the originally requested resource. The 303 response MUST
   * NOT be cached, but the response to the second (redirected) request might
   * be cacheable.
   */
  const STATUS_SEE_OTHER = '303 See Other';

  /**
   * If the client has performed a conditional GET request and access is
   * allowed, but the document has not been modified, the server SHOULD respond
   * with this status code. The 304 response MUST NOT contain a message-body,
   * and thus is always terminated by the first empty line after the header
   * fields.
   *
   * The response MUST include the following header fields:
   * - Date, unless its omission is required by section 14.18.1
   */
  const STATUS_NOT_MODIFIED = '304 Not Modified';

 /**
   * The requested resource MUST be accessed through the proxy given by the
   * Location field. The Location field gives the URI of the proxy. The
   * recipient is expected to repeat this single request via the proxy. 305
   * responses MUST only be generated by origin servers.
   */
  const STATUS_USE_PROXY = '305 Use Proxy';

  /**
   * The requested resource resides temporarily under a different URI. Since
   * the redirection MAY be altered on occasion, the client SHOULD continue
   * to use the Request-URI for future requests. This response is only cacheable
   * if indicated by a Cache-Control or Expires header field.
   *
   * The temporary URI SHOULD be given by the Location field in the response.
   * Unless the request method was HEAD, the entity of the response SHOULD
   * contain a short hypertext note with a hyperlink to the new URI(s) ,
   * since many pre-HTTP/1.1 user agents do not understand the 307 status.
   * Therefore, the note SHOULD contain the information necessary for a user to
   * repeat the original request on the new URI.
   */
  const STATUS_TEMPORARY_REDIRECT = '307 Temporary Redirect';

  /**
   * The request could not be understood by the server due to malformed syntax.
   * The client SHOULD NOT repeat the request without modifications.
   */
  const STATUS_BAD_REQUEST = '400 Bad Request';

  /**
   * The request requires user authentication. The response MUST include a
   * WWW-Authenticate header field (section 14.47) containing a challenge
   * applicable to the requested resource. The client MAY repeat the request
   * with a suitable Authorization header field (section 14.8). If the request
   * already included Authorization credentials, then the 401 response
   * indicates that authorization has been refused for those credentials.
   * If the 401 response contains the same challenge as the prior response,
   * and the user agent has already attempted authentication at least once,
   * then the user SHOULD be presented the entity that was given in the response,
   * since that entity might include relevant diagnostic information. HTTP
   * access authentication is explained in "HTTP Authentication: Basic and
   * Digest Access Authentication" [43].
   */
  const STATUS_UNAUTHORIZED = '401 Unauthororized';

  /**
   * This code is reserved for future use.
   */
  const STATUS_PAYMENT_REQUIRED = '402 Payment Required';

  /**
   * The server understood the request, but is refusing to fulfill it.
   * Authorization will not help and the request SHOULD NOT be repeated. If
   * the request method was not HEAD and the server wishes to make public why
   * the request has not been fulfilled, it SHOULD describe the reason for
   * the refusal in the entity. If the server does not wish to make this
   * information available to the client, the status code 404 (Not Found) can
   * be used instead.
   */
  const STATUS_FORBIDDEN = '403 Forbidden';
  
 /**
   * The server has not found anything matching the Request-URI. No indication 
   * is given of whether the condition is temporary or permanent. The 410 (Gone) 
   * status code SHOULD be used if the server knows, through some internally 
   * configurable mechanism, that an old resource is permanently unavailable 
   * and has no forwarding address. This status code is commonly used when the 
   * server does not wish to reveal exactly why the request has been refused, 
   * or when no other response is applicable.
   */
  const STATUS_NOT_FOUND = '404 Not Found';

  /**
   * The method specified in the Request-Line is not allowed for the resource
   * identified by the Request-URI. The response MUST include an Allow header
   * containing a list of valid methods for the requested resource.
   */
  const STATUS_METHOD_NOT_ALLOWED = '405 Method Not Allowed';

  /**
   * The resource identified by the request is only capable of generating
   * response entities which have content characteristics not acceptable
   * according to the accept headers sent in the request.
   */
  const STATUS_NOT_ACCEPTABLE = '406 Not Acceptable';

  /**
   * This code is similar to 401 (Unauthorized), but indicates that the client
   * must first authenticate itself with the proxy. The proxy MUST return a
   * Proxy-Authenticate header field (section 14.33) containing a challenge
   * applicable to the proxy for the requested resource. The client MAY repeat
   * the request with a suitable Proxy-Authorization header field
   * (section 14.34). HTTP access authentication is explained in "HTTP
   * Authentication: Basic and Digest Access Authentication" [43].
   */
  const STATUS_PROX_AUTHENTICATION_REQUIRED =
                                '407 Proxy Authentication Required';

  /**
   * The client did not produce a request within the time that the server was
   * prepared to wait. The client MAY repeat the request without modifications
   * at any later time.
   */
  const STATUS_REQUEST_TIMEOUT = '408 Request Timeout';
  
  /**
   * The request could not be completed due to a conflict with the current state 
   * of the resource. This code is only allowed in situations where it is 
   * expected that the user might be able to resolve the conflict and resubmit 
   * the request. The response body SHOULD include enough information for the 
   * user to recognize the source of the conflict. Ideally, the response entity 
   * would include enough information for the user or user agent to fix the 
   * problem; however, that might not be possible and is not required.
   */
  const STATUS_CONFLICT = '409 Conflict';

  /**
   * The requested resource is no longer available at the server and no
   * forwarding address is known. This condition is expected to be considered
   * permanent.
   */
  const STATUS_GONE = '410 Gone';

  /**
   * The server refuses to accept the request without a defined Content- Length.
   * The client MAY repeat the request if it adds a valid Content-Length header
   * field containing the length of the message-body in the request message.
   */
  const STATUS_LENGTH_REQUIRED = '411 Length Required';

  /**
   * The precondition given in one or more of the request-header fields
   * evaluated to false when it was tested on the server. This response code
   * allows the client to place preconditions on the current resource
   * metainformation (header field data) and thus prevent the requested
   * method from being applied to a resource other than the one intended.
   */
  const STATUS_PRECONDITION_FAILED = '412 Precondition Failed';

  /**
   * The server is refusing to process a request because the request entity is
   * larger than the server is willing or able to process. The server MAY
   * close the connection to prevent the client from continuing the request.
   */
  const STATUS_REQUEST_ENTITY_TOO_LARGE =
                                '413 Request Entity Too Large';
  
  /**
   * The server is refusing to service the request because the Request-URI is 
   * longer than the server is willing to interpret.
   */
  const STATUS_REQUEST_URI_TOO_LONG = '414 Request-URI Too Long';

  /**
   * The server is refusing to service the request because the entity of the
   * request is in a format not supported by the requested resource for the
   * requested method.
   */
  const STATUS_UNSUPPORTED_MEDIA_TYPE =
                                '415 Unsupported Media Type';

  /**
   * A server SHOULD return a response with this status code if a request
   * included a Range request-header field (section 14.35), and none of the
   * range-specifier values in this field overlap the current extent of the
   * selected resource, and the request did not include an If-Range
   * request-header field. (For byte-ranges, this means that the first-
   * byte-pos of all of the byte-range-spec values were greater than the
   * current length of the selected resource.)
   */
  const STATUS_REQUEST_RANGE_NOT_SATISFIABLE = '416 Request Range Not Satisfiable';

  /**
   * The expectation given in an Expect request-header field could not be met
   * by this server, or, if the server is a proxy, the server has unambiguous
   * evidence that the request could not be met by the next-hop server.
   */
  const STATUS_EXPECTATION_FAILED = '417 Expectation Failed';

  /**
   * The server encountered an unexpected condition which prevented it from
   * fulfilling the request.
   */
  const STATUS_INTERNAL_SERVER_ERROR = '500 Internal Server Error';

  /**
   * The server does not support the functionality required to fulfill the
   * request. This is the appropriate response when the server does not
   * recognize the request method and is not capable of supporting it for any
   * resource.
   */
  const STATUS_NOT_IMPLEMENTED = '501 Not Implemented';

  /**
   * The server, while acting as a gateway or proxy, received an invalid
   * response from the upstream server it accessed in attempting to fulfill
   * the request.
   */
  const STATUS_BAD_GATEWAY = '502 Bad Gateway';

  /**
   * The server is currently unable to handle the request due to a temporary
   * overloading or maintenance of the server. The implication is that this is
   * a temporary condition which will be alleviated after some delay. If known,
   * the length of the delay MAY be indicated in a Retry-After header. If no
   * Retry-After is given, the client SHOULD handle the response as it would
   * for a 500 response.
   */
  const STATUS_SERVICE_UNAVAILABLE = '503 Service Unavailable';
  
  /**
   * The server, while acting as a gateway or proxy, did not receive a timely 
   * response from the upstream server specified by the URI (e.g. HTTP, FTP, 
   * LDAP) or some other auxiliary server (e.g. DNS) it needed to access in 
   * attempting to complete the request.
   */
  const STATUS_GATEWAY_TIMEOUT = '504 Gateway Timeout';

  /**
   * The server does not support, or refuses to support, the HTTP protocol
   * version that was used in the request message. The server is indicating
   * that it is unable or unwilling to complete the request using the same
   * major version as the client, as described in section 3.1, other than with
   * this error message. The response SHOULD contain an entity describing why
   * that version is not supported and what other protocols are supported by
   * that server.
   */
  const STATUS_HTTP_VERSION_NOT_SUPPORTED = '505 HTTP Version Not Supported';
}
?>