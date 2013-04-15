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
 * Just a bunch of constants to help in choosing a compression type in headers.
 * The information is pretty much all from 
 * {@link http://en.wikipedia.org/wiki/HTTP_compression Wikipedia}
 * 
 * @package SimpleHTTP
 * @author
 */

class HTTPCompression {
  /**
   * UNIX "compress" program method
   * @link http://en.wikipedia.org/wiki/Compress
   */
  const COMPRESS = 'compress';
  
  /**
   * despite its name the zlib compression {@link http://tools.ietf.org/html/rfc1950(RFC 1950)}
   *  should be used(in combination with the deflate compression {@link 
   * http://tools.ietf.org/html/rfc1951(RFC 1951)}) as described in 
   * the {@link http://tools.ietf.org/html/rfc2616 RFC 2616}. The 
   * implementation in the real world however seems to vary between the zlib 
   * compression and the (raw) deflate compression. Due to this confusion, 
   * gzip has positioned itself as the more reliable default 
   * method.
   */
  const DEFLATE = 'deflate';
  
  /**
   * W3C Efficient XML Interchange
   * @link http://en.wikipedia.org/wiki/Efficient_XML_Interchange
   */
  const EXI = 'exi';
  
  /**
   * GNU zip format (described in {@link http://tools.ietf.org/html/rfc1952 RFC 
   * 1952)}. This method is the most broadly supported as of March 2011
   * @link http://en.wikipedia.org/wiki/Gzip
   */
  const GZIP = 'gzip';
  
  /**
   * No transformation is used. This is the default value for content coding.
   */
  const IDENTITY = 'indentity';
  
  /**
   * Network Transfer Format for Java Archives
   * @link http://en.wikipedia.org/wiki/Pack200
   */
  const PACK200_GZIP = 'pack200-gzip';
  
  /**
   * Google Shared Dictionary Compression for HTTP
   * @link http://en.wikipedia.org/wiki/SDCH
   */
  const SDCH = 'sdch';
  
  /**
   * free and open source lossless data compression algorithm
   * @link http://en.wikipedia.org/wiki/Bzip2
   */
  const BZIP2 = 'bzip2';
  
  /**
   * Microsoft Peer Content Caching and Retrieval
   */
  const PEERDIST= 'peerdist';
}
?>