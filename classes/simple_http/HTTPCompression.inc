<?php
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