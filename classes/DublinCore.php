<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/*
 *    This file is part of curation_tool.

 *    curation_tool is free software: you can redistribute it and/or modify
 *    it under the terms of the Apache License, Version 2.0 (See License at the
 *    top of the directory).

 *    curation_tool is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

 *    You should have received a copy of the Apache License, Version 2.0
 *    along with curation_tool.  If not, see <http://www.apache.org/licenses/LICENSE-2.0.html>.
 */

/**
 * A utility class to quickly get Dublin Core elements and terms.
 *
 * @author olendorf
 * 
 */
class DublinCore {

  /**
   * Namespace URI for Dublin Core terms
   * @var string 
   */
  public $termsURI = 'http://purl.org/dc/terms/';
  
  /**
   *
   * @var string 
   */
  public $termsSchemaLocation = 'http://dublincore.org/schemas/xmls/qdc/2008/02/11/dcterms.xsd';
  
  /**
   * Standard prefix for DC Terms
   * @var string 
   */
  public $termsPrefix = 'dcterms';
  
  /**
   * Namespace URI for Dublin Core elements
   * @var string 
   */
  public $elementsURI = 'http://purl.org/dc/elements/1.1/';
  
  /**
   *
   * @var string 
   */
  public $elementsSchemaLocation = 'http://dublincore.org/schemas/xmls/qdc/2008/02/11/dc.xsd';
  
  /**
   * Standard prefix for DC Elements
   * @var string 
   */
  public $elementsPrefix = 'dc';
  
  public function get_elements() {
      return array(
        'title',
        'creator',
        'subject',
        'description',
        'publisher',
        'contributor',
        'date',
        'type',
        'format',
        'identifier',
        'source',
        'language',
        'relation',
        'coverage',
        'rights',
    );
  }
  
  public function get_terms() {
    return array(
        'abstract',
        'accessRights',
        'accrualMethod',
        'accrualPeriodicity',
        'accrualPolicy',
        'alternative',
        'audience',
        'available',
        'bibliographicCitation',
        'conformsTo',
        'contributor',
        'coverage',
        'created',
        'creator',
        'date',
        'dateAccepted',
        'dateCopyrighted',
        'dateSubmitted',
        'description',
        'educationLevel',
        'extent',
        'format',
        'hasFormat',
        'hasPart',
        'hasVersion',
        'identifier',
        'instructionalMethod',
        'isFormatOf',
        'isPartOf',
        'isReferencedBy',
        'isReplacedBy',
        'isRequiredBy',
        'issued',
        'isVersionOf',
        'language',
        'license',
        'mediator',
        'medium',
        'modified',
        'provenance',
        'publisher',
        'references',
        'relation',
        'replaces',
        'requires',
        'rights',
        'rightsHolder',
        'source',
        'spatial',
        'subject',
        'tableOfContents',
        'temporal',
        'title',
        'type',
        'valid',
    );
  }
}

?>
