These is are base classes and interfaces for general HTTP responses and requests.
There is a PECL based class, but its not supported on all web hosting services.
These classes should parallel those fairly closely.

Installation and Use.
=====================
Just place the files in your typical class location. You can include or require
each file individually, or you can include the entire library by using
require_once('file/path/SimpleHTT.inc'), where file path will depend on where
you place the folders.

Several files, are merely large lists of constants to facilitate inclusion
of commonly used constants such as HTTP Response Codes into your code easily. I've
tried to steal a lot from other sources to document the constants in PHPDocumentor
style well enough to make them easy to use.

File Descriptions
====================

HTTPCompressions.inc: Constants to allow easy inclusion of commonly used compression
types.

HTTPRequestHeaders.inc: W3C specified request and commonly used others
such as mimetype application/x-www-form-urlencoded. Their definitions are included

HTTPResponseHeaders.inc: W3C specified and other commonly used response headers
along with their definitions.

HTTPStatusCodes.inc: W3C specified HTTP Response Codes such as 404 Not Found.

SimpleHTTP.inc: simple bootstrapping file. Include this to load the entire library.

SimpleHTTPRequest.php: A class to facilitate making requests from a php script.

SimpleHTTPResponse.php: A class to facilitate creating customized responses to
requests. Allows the insertion of custom headers into the response.