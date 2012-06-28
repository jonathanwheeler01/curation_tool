<?php
/**
 * 
 * Implements a form to allow users to create a new dta project.
 * @author Robert Olendorf
 * @license Apache License 2.0
 *  
 */



/**
 * Form for creating a new data curation project.
 * 
 * @param array $form
 * @param array $form_state
 * @return array 
 */
function curation_tool_new_project_form($form, &$form_state) {
  global $user;
  $user_fields = user_load($user->uid);
  
  $form['data'] = array(
      '#type' => 'fieldset',
      '#title' => t('Data'),
      '#Description' => t('Upload a new data set to process or choose an existing data set.'),
  );
  
  $form['data']['upload_data'] = array(
      '#type' => 'file',
      '#title' => t('Upload and Process New Data'),
      '#description' => t('Upload a single file, or a zipped file for mutliple files.'),
      );
  
  $form['data']['existing_data'] = array(
      '#title' => t('Process Existing Data'),
      '#type' => 'textfield',
      '#length' => 80,
      '#maxlenght' => 256,
  );
  
  $form['curator_info'] = array(
      '#type' => 'fieldset',
      '#title' => t('Curator Info'),
      '#description' => t('Enter information about the curators and curatoin process here.'),
  );
  
  $form['curator_info']['curator'] = array(
      '#title' => t('Curator&apos;s Name(s)'),
      '#description' => t('The name(s) of the individuals responsible for '.
              'curating this data set. Separate names with commas.'),
      '#type' => 'textfield',
      '#length' => 60,
      '#maxlength' => 120,
      '#required' => TRUE,
      );
  
  $form['curator_info']['metaCreateDate'] = array(
      '#title' => t('Creation Date'),
      '#type' => 'textfield',
      '#length' => 60,
      '#default_value' => date('c', time()),
      '#required' => TRUE,
  );
  
  $form['descriptive_metadata'] = array(
      '#title' => t('Descriptive Information'),
      '#description' => t('Use these fields to supply information that applies to the '.
              'entire data set'),
      '#type' => 'fieldset',
  );
  
  $form['descriptive_metadata']['title'] = array(
      '#title' => t('Title'),
      '#description' => t('Provide a short descriptive title for the data.'),
      '#type' => 'textfield',
      '#length' => 80,
      '#maxlength' => 120,
      '#required' => TRUE,
  );
  
  $form['descriptive_metadata']['creator'] = array(
      '#title' => t('Data Creators'),
      '#description' => t('Enter the names of the people responsible for creating the data '.
              'separated by commas.'),
      '#type' => 'textfield',
      '#length' => 80,
      '#maxlength' => 256,
      '#required' => TRUE,
  );
  
  $form['descriptive_metadata']['contributor'] = array(
      '#title' => t('Other Contributors'),
      '#description' => t('Enter the names of other people with a role in creating the data '.
              'separated by commas.'),
      '#type' => 'textfield',
      '#length' => 80,
      '#maxlength' => 256,
  );
  
  $form['descriptive_metadata']['subject'] = array(
      '#title' => t('Keywords'),
      '#description' => t('Enter keywords or phrases to aid in searching for your data separated by commas.'.
              'separated by commas.'),
      '#type' => 'textfield',
      '#length' => 80,
      '#maxlength' => 256,
  );
  
  $form['descriptive_metadata']['dataCreated'] = array(
      '#title' => t('Date Created'),
      '#description' => t('Enter the date the data was created <em>(YYYY-MM-DD)</em>'),
      '#type' => 'textfield',
      '#length' => 80,
      '#maxlength' => 256,
      '#default_value' => date('Y-m-d', time()),
      '#required' => TRUE,
  );
  
  $form['descriptive_metadata']['abstract'] = array(
      '#title' => t('Describe The Study'),
      '#description' => t('Provide a narrative description of the project.'),
      '#type' => 'textarea',
      '#cols' => 80,
      '#rows' => 10,
      '#resizeable' => TRUE,
  );
  
  $form['descriptive_metadata']['description'] = array(
      '#title' => t('Describe the Data'),
      '#description' => t('Provide a brief description of the structure of the data.'),
      '#type' => 'textarea',
      '#cols' => 80,
      '#rows' => 10,
      '#resizeable' => TRUE,
  );
  
  $form['license_metadata'] = array(
      '#type' => 'fieldset',
      '#title' => 'Rights and Licensing Information',
      '#description' => 'Use this section to document licensing and rights issues '.
              'concerning your entire dataset. If only '.
              'parts of your dataset can be claimed, you may document that specifically '.
              'for those files or groups of file later.',
  );
  
  $form['license_metadata']['license'] = array(
      '#title' => t('Licensing Information'),
      '#description' => t('Describe how you want your data licensed.'),
      '#type' => 'textarea',
      '#cols' => 80,
      '#rows' => 10,
      '#default_value' => 'This work is licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.',
      '#resizeable' => TRUE,
  );
  
  $form['license_metadata']['rights'] = array(
      '#title' => t('Rights Information'),
      '#description' => t('If any other parties can claim rights over this data set, list them and describe the nautre of their claim.' ),
      '#type' => 'textarea',
      '#cols' => 80,
      '#rows' => 10,
      '#resizeable' => TRUE,
  );
  
  $form['assurances'] = array(
      '#title' => t('Assurances'),
      '#type' => 'fieldset',
      '#description' => 'Please read the following assurances. Each box '.
                        'must be checked to upload the data. By checking the boxes '.
                        'you are stating that the statements are true.',
  );
  
  $form['assurances']['assureCopyright'] = array(
      '#type' => 'checkbox',
      '#description' => 'I am the owner of this content or have permission to release '.
                        'this content to the University of New Mexico Institutional Repository (LoboVault).',
//      '#required' => TRUE,
  );
  
  $form['assurances']['releaseCopyright'] = array(
      '#type' => 'checkbox',
      '#description' => 'I understand that LoboVault is an open access repository and '.
                        'that the content I am uploaded will be available to the public '.
                        'immediately or after a specified embargo period of less than 2 years.',
//      '#required' => TRUE,
  );
  
  $form['assurances']['assureInformation'] = array(
      '#type' => 'checkbox',
      '#description' => 'I have supplied sufficient supporting information to make this data '.
                        'understandable and accessable to others.',
//      '#required' => TRUE,
  );
  
  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Submit',
      '#submit' => array('curation_tool_new_project_submit'),
      '#validate' => array('curation_tool_new_project_validate'),
  );
  return $form;
}

/**
 * @todo validate dates
 * @todo validate assurances
 * @todo check all other possible validations.
 * @param type $form
 * @param type $form_state 
 */
function curation_tool_new_project_validate($form, &$form_state) {
  if(! $form_state['values']['upload_data'] xor 
          $form_state['values']['existing_data']) {
    form_set_error('data', t('You must enter either the location of the '.
            'existing data or upload data, but not both.'));
  }
}

/**
 * A helper funciton to add a bunch of namespaced elements to a document.
 * @param DOMDocument $doc
 * @param array $data
 * @param string $namespaceURI
 * @param string $qname
 * @param DOMElement $parent 
 */
function append_elementNS(DOMDocument &$doc, array $data, $namespaceURI, $qname, DOMElement $parent = null) {
  foreach($data as $value) {
    if($parent) {
      $parent->appendChild($element = $doc->createElementNS($namespaceURI, $qname));
    }
    else {
      $doc->appendChild($element = $doc->createElementNS($namespaceURI, $qname));
    }
    $element->appendChild($doc->createTextNode($value));
  }
}

/**
 * @todo this is currently FUUUUGGGLLLYYY. However it works. Consider cleaning up the
 * code.
 * @param type $form
 * @param type $form_state 
 */
function curation_tool_new_project_submit($form, &$form_state) {

  drupal_set_message('IT WORKED!!!<br/>');
  
  $settings = new XFDUSetup();  
  
  //Set the location of the data to be processed. Since either one or the other
  // must be specified, we just set the value of both. This acts as a switch
  // on the data processing system.
  $settings->upload = $form_state['values']['upload_data'];
  $settings->existing = $form_state['values']['existing_data'];
  $settings->repository = variable_get('repository_root');
  
  $dom = new DOMDocument('1.0', 'UTF-8');
  $dcURI = 'http://purl.org/dc/elements/1.1/';
  $dcPrefix = 'dc';
  $termsURI = 'http://purl.org/dc/terms/';
  $termsPrefix = 'dcterms';
  
  ////////////////////////
  // Handle the packagheader metadata
  
  $anyXML = $dom->appendChild($wrapper = $dom->createElement('wrapper'));
  $anyXML->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:'.$dcPrefix, $dcURI);
  $anyXML->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:'.$termsPrefix, $termsURI);
 

  
  // Handle metadata about the curators.
  append_elementNS(
          $dom, 
          explode(',', $form_state['values']['curator']), 
          $dcURI,
          $dcPrefix.':creator',
          $anyXML);
  
  // Handle the creation date for the metadata
  append_elementNS(
          $dom, 
          array($form_state['values']['metaCreateDate']), 
          $dcURI, 
          $dcPrefix.':created', 
          $anyXML
          );
  
  $settings->xmlData = $dom->saveXML();
  
  /////////////////////////////////////////////
  // Create a new dom document and handle the descriptive
  // metadata for the data.
  
  $dom = new DOMDocument('1.0', 'UTF-8');

  $dmd = $dom->appendChild($wrapper = $dom->createElement('wrapper'));
  $dmd->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:'.$dcPrefix, $dcURI);
  $dmd->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:'.$termsPrefix, $termsURI);
  
  // Handle the name of the data  
  append_elementNS(
          $dom, 
          array($form_state['values']['title']), 
          $dcURI, 
          $dcPrefix.':title', 
          $dmd
          );
  
  // Handle the creators of the data.  
  append_elementNS(
          $dom, 
          explode(',', $form_state['values']['creator']), 
          $dcURI, 
          $dcPrefix.':creator', 
          $dmd
          );
  
  // Handle contributors to the data
  if($form_state['values']['contributor']) {
    append_elementNS(
            $dom, 
            explode(',', $form_state['values']['contributor']), 
            $dcURI, 
            $dcPrefix.':contributor', 
            $dmd
            );
    }  
  
  // Handle any subjects or keywords supplied
  if($form_state['values']['subject']) {
    append_elementNS(
            $dom, 
            explode(',', $form_state['values']['subject']), 
            $dcURI, 
            $dcPrefix.':subject', 
            $dmd
            );
    }    
  
  // Handle any subjects or keywords supplied
  if($form_state['values']['dataCreated']) {
    append_elementNS(
            $dom, 
            array($form_state['values']['dataCreated']), 
            $termsURI, 
            $termsPrefix.':created', 
            $dmd
            );
  }
  
  //Handle the data abstract.
  if($form_state['values']['abstract']) {
    append_elementNS(
            $dom, 
            array($form_state['values']['abstract']), 
            $termsURI, 
            $termsPrefix.':abstract', 
            $dmd
            );
  }
  
  //Handle the description.
  if($form_state['values']['description']) {
    append_elementNS(
            $dom, 
            array($form_state['values']['description']), 
            $dcURI, 
            $dcPrefix.':description', 
            $dmd
            );
  }
  
  //Handle the licensing information
  if($form_state['values']['license']) {
    append_elementNS(
            $dom, 
            array($form_state['values']['license']), 
            $termsURI, 
            $termsPrefix.':license', 
            $dmd
            );
  }
  
  //Handle the rights information
  if($form_state['values']['rights']) {
    append_elementNS(
            $dom, 
            array($form_state['values']['rights']), 
            $termsURI, 
            $termsPrefix.':rights', 
            $dmd
            );
  }
  

  
//  drupal_set_message(base64_encode(json_encode($settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)));
  drupal_set_message(json_encode($settings, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));

  //$form_state['redirect'] = '';
}

?>
