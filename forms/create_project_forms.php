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
  
  // Initialize the page if needed to avoid invalid index errors
  if(empty($form_state['page'])) {
    $form_state['page'] = 1;
  }

  // Designate the reaquired form and call it
  $formName = 'curation_tool_new_project_form_page_'.$form_state['page'];
  return $formName($form, $form_state);
}

/**
 * 
 * @global type $user
 * @param type $form
 * @param type $form_state
 * @return array returns the form
 */
function curation_tool_new_project_form_page_1($form, &$form_state) {    
  global $user;
  $user = user_load($user->uid);
  
  $form['data'] = array(
      '#type' => 'fieldset',
      '#title' => t('Data'),
      '#Description' => t('Upload a new data set to process or choose an existing data set.'),
  );
  
  // Get a list of the usernames for the drop down list
  $usernames = db_select('users', 'u')
                    ->fields('u', array('name'))
                    ->execute();
  
  $options = array();
  
  // Populate the data for the dropdown list
  while($name = $usernames->fetchAssoc()) {
    $userInfo = user_load_by_name($name);
    $firstName = field_get_items('user', $userInfo, 'field_first_name');
    $lastName = field_get_items('user', $userInfo, 'field_last_name');
    $options[$name['name']] = $firstName[0]['value'].' '.$lastName[0]['value'].' - '.$name['name'];
  }
  
  // Set the default value for the dropdown list
  if(empty($form_state['page_values'][1]['username'])) {
    $firstName = field_get_items('user', $user, 'field_first_name');
    $lastName = field_get_items('user', $user, 'field_last_name');
    $dataowner = $firstName[0]['value'].' '.$lastName[0]['value'].' - '.$user->name;
  }
  else {    
    $dataowner = $form_state['page_values'][1]['username'];
  }
  
  $form['data']['username'] = array(
      '#title' => 'Banner ID of the Project Owner',
      '#type' => 'select',
      '#default_value' => $dataowner,
      '#access' => user_access('create any project'),
      '#required' => TRUE,
      '#options' => $options,
  ); 
  
  $form['data']['upload_data'] = array(
      '#name' => 'files[upload_data]',
      '#type' => 'file',
      '#title' => t('Upload and Process New Data'),
      '#description' => t('Upload a single file, or a zipped file for mutliple files.'),
      );
  
  $form['curator_info'] = array(
      '#type' => 'fieldset',
      '#title' => t('Curator Info'),
      '#description' => t('Enter information about the curators and curatoin process here.'),
  );

  // If the curators have been specified previously use them. Otherwise
  // set the default to the curent user
  if(empty($form_state['page_values'][1]['curator'])) {
    $firstName = field_get_items('user', $user, 'field_first_name');
    $lastName = field_get_items('user', $user, 'field_last_name');
    $curator = $firstName[0]['value'].' '.$lastName[0]['value'];
  }
  else {
    $curator = $form_state['page_values'][1]['curator'];
  }
  
  $form['curator_info']['curator'] = array(
      '#title' => t('Curator&apos;s Name(s)'),
      '#description' => t('The name(s) of the individuals responsible for '.
              'curating this data set. Separate names with vertical bars (|).'),
      '#type' => 'textfield',
      '#length' => 60,
      '#maxlength' => 120,
      '#required' => TRUE,
      '#default_value' => $curator,
      );
  
  // Recover any prevoiusly specfied data
  if(empty($form_state['page_values'][1]['metaCreateDate'])) {
    $metaCreateDate = date('c', time());
  }
  else {
    $metaCreateDate = $form_state['page_values'][1]['metaCreateDate'];
  }
  
  $form['curator_info']['metaCreateDate'] = array(
      '#title' => t('Creation Date'),
      '#type' => 'textfield',
      '#length' => 60,
      '#default_value' => $metaCreateDate,
      '#required' => TRUE,
      '#description' => t('Enter the date the project data was uploaded.'),
  );
  
  $form['next'] = array(
      '#type' => 'submit',
      '#value' => 'Next >> ',
      '#submit' => array('curation_tool_new_project_form_next'),
      '#validate' => array('curation_tool_new_project_page_1_validate'),
  );
  
  return $form;
}

/**
 * Handles pagination everytime the next button is pushed.
 * @param type $form
 * @param type $form_state 
 */
function curation_tool_new_project_form_next($form, &$form_state) {
  // Ensure that values for each page are carried forward. 
  $form_state['page_values'][$form_state['page']] = $form_state['values'];
 
  // Call the submit function for this form to handle any processing required.
  $submitFunction = 'curation_tool_new_project_page_'.$form_state['page'].'_submit';
  $submitFunction($form, $form_state);
  
  // Increment the page and rebuild the form to call the hook_form implementation
  $form_state['page']++;
  $form_state['rebuild'] = TRUE;
}

/**
 * Handles back button submits.
 * @param type $form
 * @param type $form_state 
 */
function curation_tool_new_project_form_back($form, &$form_state) {
  $form_state['values'] = $form_state['page_values'][1];
  $form_state['page']--;
  $form_state['rebuild'] = TRUE;
}

/**
 * Handles cancellations
 * @param type $form
 * @param type $form_state 
 */
function curation_tool_new_project_form_cancel($form, &$form_state) {
  file_delete($form_state['storage']['file'], TRUE);
  $form_state['values'] = $form_state['page_values'][1];
  $form_state['page'] = 1;
  $form_state['rebuild'] = TRUE;
}

/**
 * Validates data from form 1.
 * @param type $form
 * @param type $form_state 
 */
function curation_tool_new_project_page_1_validate($form, &$form_state) {

  
  // Big nasty regex to validate a date time
 $dateTimeRegex = 
        '/^-?((?:19|20[0-9]{2})-(?:0[1-9]|1[0-2])-(?:0[1-9]|[1-2][0-9]|3[0-1]))'.
        'T((?:[0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9])(?:.[0-9]{0,9})?'.
        '((?:Z)|(?:[-\+](?:[0-1][0-9]|2[0-3]):00))?$/';

 // Further validation can be done on the $matches variable. The capture groups are
 // [0] => date, [1] => time,[2] => timezone specification. 
  if(!preg_match($dateTimeRegex, $form_state['values']['metaCreateDate'], $matches)) {
    form_set_error(
            'values][metaCreateDate', 
            'The creation date for the metadata must conform to the format '.
            '"YYYY-MM-DDTHH:MM:SS" with an optional timezone designation of Z '.
            'or +/-HH:00. (example: "'.date('c', time()).'")'
            );
  }
  
  // Attempt to upload the file and validate it.
  $validators = array(
      'file_validate_size' => array(file_upload_max_size()),
      'file_validate_extensions' => array(),
  );
  
  $file = file_save_upload('upload_data', $validators);
  // File passed validation so handle the rest.
  if($file) {
    
    // Avoid accidentally overwriting existing projects.
    $dirName = implode('.', array_slice(explode('.', $file->filename), 0, -1));
    if(_check_project_exisits($dirName, $form_state['values']['username'])) {
      if(user_access('delete any project')) {
        $message = 'The project "'.$dirName.'" already exisits. If you wish '.
                'to overwrite the project, continue normally, otherwise cancel to '.
                'avoid losing previous work.';
        drupal_set_message($message, 'warning');
      }
      else {
        $message = 'The project "'.$dirName.'" already exisits. Please choose a '.
              'file with a different name or <a href="mailto:'.  
                variable_get('data_curation_help_email').'">contact us</a> if you '.
                'feel this message has been recieved in error.';
        form_set_error('data][upload_data', $message);
      }
    }
    $form_state['storage']['file'] = $file;
  }
  else {
    $message = 'There was a problem uploading your file. Ensure you chose a valid '.
            'file and that your your file size is below the maximum allowable size of '.
            (file_upload_max_size()/1048576).'MB. If your file size is to large '.
            'or if you continue to have other problems please don&apos;t hesitate '.
            'to <a href="mailto:'.  variable_get('data_curation_help_email').'">contact us</a>.';
    form_set_error('data][upload_data', $message);
  }
  

}

/**
 * Stub function to allow for extension to handles the submission process for 
 * form 1. Any specific submission handling goes here.
 * @param type $form
 * @param type $form_state 
 */
function curation_tool_new_project_page_1_submit($form, &$form_state){ 
    
}

/**
 *
 * @param type $form
 * @param type $form_state
 * @return string 
 */
function curation_tool_new_project_form_page_2($form, &$form_state) {
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
      '#default_value' => 
            key_exists(2, $form_state['page_values'])?
                                $form_state['page_values'][2]['title']:'',
  );
  
  $form['descriptive_metadata']['creator'] = array(
      '#title' => t('Data Creators'),
      '#description' => t('Enter the names of the people responsible for creating the data '.
              'separated with vertical bars (|).'),
      '#type' => 'textfield',
      '#multi' => TRUE,
      '#length' => 80,
      '#maxlength' => 256,
      '#required' => TRUE,
      '#default_value' => 
            key_exists(2, $form_state['page_values'])?
                                $form_state['page_values'][2]['creator']:'',
      
  );
  
  $form['descriptive_metadata']['contributor'] = array(
      '#title' => t('Other Contributors'),
      '#description' => t('Enter the names of other people with a role in creating the data '.
              'separated with vertical bars (|).'),
      '#type' => 'textfield',
      '#multi' => TRUE,
      '#length' => 80,
      '#maxlength' => 256,
      '#default_value' => 
            key_exists(2, $form_state['page_values'])?
                                $form_state['page_values'][2]['contributor']:'',
  );
  
  $form['descriptive_metadata']['subject'] = array(
      '#title' => t('Keywords'),
      '#description' => t('Enter keywords or phrases to aid in searching for your data separated by commas.'.
              'separated by commas.'),
      '#type' => 'textfield',
      '#multi' => TRUE,
      '#length' => 80,
      '#maxlength' => 256,
      '#default_value' => 
            key_exists(2, $form_state['page_values'])?
                                $form_state['page_values'][2]['subject']:'',
  );
  
  $form['descriptive_metadata']['created'] = array(
      '#title' => t('Date Created'),
      '#description' => t('Enter the date the data was created <em>(YYYY-MM-DD)</em>'),
      '#type' => 'textfield',
      '#length' => 80,
      '#maxlength' => 256,
      '#default_value' => date('Y-m-d', time()),
      '#required' => TRUE,
      '#default_value' => 
            key_exists(2, $form_state['page_values'])?
                                $form_state['page_values'][2]['created']:'',
  );
  
  $form['descriptive_metadata']['abstract'] = array(
      '#title' => t('Describe The Study'),
      '#description' => t('Provide a narrative description of the project.'),
      '#type' => 'textarea',
      '#cols' => 80,
      '#rows' => 10,
      '#resizeable' => TRUE,
      '#default_value' => 
            key_exists(2, $form_state['page_values'])?
                                $form_state['page_values'][2]['abstract']:'',
  );
  
  $form['descriptive_metadata']['description'] = array(
      '#title' => t('Describe the Data'),
      '#description' => t('Provide a brief description of the structure of the data.'),
      '#type' => 'textarea',
      '#cols' => 80,
      '#rows' => 10,
      '#resizeable' => TRUE,
      '#default_value' => 
            key_exists(2, $form_state['page_values'])?
                                $form_state['page_values'][2]['description']:'',
  );
  
  $form['back'] = array(
      '#type' => 'submit',
      '#value' => '<< Back',
      '#submit' => array('curation_tool_new_project_form_back'),
      '#limit_validation_errors' => array(),
  );
  
  $form['cancel'] = array(
      '#type' => 'submit',
      '#value' => '<< Cancel >>',
      '#submit' => array('curation_tool_new_project_form_cancel'),
      '#limit_validation_errors' => array(),
  );
  
  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Next >>',
      '#submit' => array('curation_tool_new_project_form_next'),
      '#validate' => array('curation_tool_new_project_page_2_validate'),
  );
  return $form;
}

/**
 * Returns a DOMDocument with a wrapper element containing the namespaces
 * for dublin core elements and dublin core tersm.
 * @return \DOMDocument 
 */
function _get_dublin_core_wrapper() {
  
  $dc = new DublinCore();
  // Create the document for the descriptive metadata. Use a wrapper to collect
  // the elements, which isn't technically required, but makes handling easier.
  $doc = new DOMDocument('1.0', 'UTF-8');
  $doc->appendChild($wrapper = $doc->createElement('wrapper'));
  
  // Add the dublin core namespaces to the wrapper.
  $wrapper->setAttributeNS(
          'http://www.w3.org/2000/xmlns/', 
          'xmlns:'.$dc->elementsPrefix, 
          $dc->elementsURI);
  $wrapper->setAttributeNS(
          'http://www.w3.org/2000/xmlns/', 
          'xmlns:'.$dc->termsPrefix, 
          $dc->termsURI);
  
  return $doc;
}

function _get_header_meta(&$form_state) {
  $dc = new DublinCore();
  $doc = new DOMDocument('1.0', 'UTF-8');
  $doc = _get_dublin_core_wrapper();
  $list = $doc->getElementsByTagName('wrapper');
  $wrapper = $list->item(0);
  
  if($form_state['values']['curator'] != '') {
    $curators = explode('|', $form_state['values']['curator']);
    foreach($curators as $curator) {
      $wrapper->appendChild(
              $newElement = $doc->createElementNS(
                      $dc->elementsURI, 
                      $dc->elementsPrefix.':contributor')
              );
      $newElement->appendChild($doc->createTextNode($curator));
    }
  }
  
  if($form_state['values']['metaCreateDate'] != '') {
      $wrapper->appendChild(
              $newElement = $doc->createElementNS(
                      $dc->termsURI, 
                      $dc->termsPrefix.':created')
              );
      $newElement->appendChild(
              $doc->createTextNode($form_state['values']['metaCreateDate'])
              );
  }
  return $doc->saveXML();
}

/**
 * Returns all currently set dublin core metadata elements from the form as 
 * XML serialzed into a string. The elements must be findable in 
 * <code>$form_state['values'] and be named by their dublin core name in lower case.
 * 
 * @param array $form_state
 * @return string The XML serialized as a string 
 */
function _get_descriptive_meta(&$form_state) {
  // Get a list of the dublin core terms and elemetns
  $dc = new DublinCore();
  $dcElements = $dc->get_elements();
  $dcTerms = $dc->get_terms();
  
  // Extract the dc terms and elements from the form
  $elements = array_intersect_key($form_state['values'], array_flip($dcElements));
  $terms = array_diff_key(array_intersect_key($form_state['values'], array_flip($dcTerms)), $elements);
  
  // Create the document for the descriptive metadata. Use a wrapper to collect
  // the elements, which isn't technically required, but makes handling easier.
  $doc = new DOMDocument('1.0', 'UTF-8');
  $doc  = _get_dublin_core_wrapper();
  
  // handle the dc elements
  foreach($elements as $name => $value) {
    // Ignore empty fields
    if($value != '') {
      // We allow multiples of elements with commas
      $values = explode('|', $value);
      foreach($values as $value) {
        $wrapper->appendChild(
                $newElement = $doc->createElementNS(
                        $dc->elementsURI, 
                        $dc->elementsPrefix.':'.$name
                        )
                );
        $newElement->appendChild($doc->createTextNode($value));
      }
    }
  }
  

  // handle the dc terms
  foreach($terms as $name => $value) {
    // Ignore empty fields
    if($value != '') {
      // We allow multiples of elements with commas
      $values = explode('|', $value);
      foreach($values as $value) {
        $wrapper->appendChild(
                $newElement = $doc->createElementNS(
                        $dc->termsURI, 
                        $dc->termsPrefix.':'.$name
                        )
                );
        $newElement->appendChild($doc->createTextNode($value));
      }
    }
  }
  
  return $doc->saveXML();
}

/**
 * Validate page two
 * @param type $form
 * @param type $form_state 
 */
function curation_tool_new_project_page_2_validate($form, &$form_state) {
 
  // Check for correct date format
  if(preg_match(
          '/^(19|20[0-9]{2})-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 
          $form_state['values']['created'], $match)
          ) {
    // Check that the date is valid.
    if(!checkdate($match[2], $match[3], $match[1])) {
      $message = $form_state['values']['dataCreated'].' is not a valid date.';
      form_set_error('descriptive_metadata][created', $message);
    }
  }
  else {
    $message = 'You must specify a valid date in the form of YYYY-MM-DD';
    form_set_error('descriptive_metadata][created', $message);
  }
}

/**
 * Stub function to allow for extension to handles the submission process for 
 * form 2. Any specific submission handling goes here.
 * @param type $form
 * @param type $form_state 
 */
function curation_tool_new_project_page_2_submit($form, &$form_state){
}

/**
 *
 * @param type $form
 * @param type $form_state
 * @return string 
 */
function curation_tool_new_project_form_page_3($form, &$form_state) {
  $form['license_metadata'] = array(
      '#type' => 'fieldset',
      '#title' => 'Rights and Licensing Information',
      '#description' => 'Use this section to document licensing and rights issues '.
              'concerning your entire dataset. If only '.
              'parts of your dataset can be claimed, you may document that specifically '.
              'for those files or groups of file later.',
  );
  
  if(empty($form_state['page_values'][3]['license'])) {
    $defaultValue = 'This work is licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.';
  }
  else {
    $defaultValue = $form_state['page_values'][3]['license'];
  }
  
  $form['license_metadata']['license'] = array(
      '#title' => t('Licensing Information'),
      '#description' => t('Describe how you want your data licensed. <br/><strong>NOTE: '.
              'Your work must have an open license to be included in the repository.</strong>'),
      '#type' => 'textarea',
      '#cols' => 80,
      '#rows' => 10,
      '#default_value' => $defaultValue,
      '#resizeable' => TRUE,
  );
  
  if(empty($form_state['page_values'][3]['rights'])) {
    $defaultValue = '';
  }
  else {
    $defaultValue = $form_state['page_values'][3]['rights'];
  }
  
  $form['license_metadata']['rights'] = array(
      '#title' => t('Rights Information'),
      '#description' => t('If any other parties can claim rights over this data set, list them and describe the nautre of their claim.' ),
      '#type' => 'textarea',
      '#cols' => 80,
      '#rows' => 10,
      '#resizeable' => TRUE,
      '#default_value' => $defaultValue,
  );
  
  $form['back'] = array(
      '#type' => 'submit',
      '#value' => '<< Back',
      '#submit' => array('curation_tool_new_project_form_back'),
      '#limit_validation_errors' => array()
  );
  
  $form['cancel'] = array(
      '#type' => 'submit',
      '#value' => '<< Cancel >>',
      '#submit' => array('curation_tool_new_project_form_cancel'),
      '#limit_validation_errors' => array(),
  );
  
  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Next >>',
      '#submit' => array('curation_tool_new_project_form_next'),
      '#validate' => array('curation_tool_new_project_page_3_validate'),
  );
  
  return $form;
}

/**
 *
 * @param type $form
 * @param type $form_state 
 */
function curation_tool_new_project_page_3_validate($form, &$form_state) {
  
}

/**
 * Stub function to allow for extension to handles the submission process for 
 * form 3. Any specific submission handling goes here.
 * @param type $form
 * @param type $form_state 
 */
function curation_tool_new_project_page_3_submit($form, &$form_state){}

/**
 *
 * @param type $form
 * @param type $form_state
 * @return array 
 */
function curation_tool_new_project_form_page_4($form, &$form_state) {
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
  );
  
  $form['assurances']['releaseCopyright'] = array(
      '#type' => 'checkbox',
      '#description' => 'I understand that LoboVault is an open access repository and '.
                        'that the content I am uploaded will be available to the public '.
                        'immediately or after a specified embargo period of less than 2 years.',
  );
  
  $form['assurances']['assureInformation'] = array(
      '#type' => 'checkbox',
      '#description' => 'I have supplied sufficient supporting information to make this data '.
                        'understandable and accessable to others.',
  );
  
  $form['back'] = array(
      '#type' => 'submit',
      '#value' => '<< Back',
      '#submit' => array('curation_tool_new_project_form_back'),
      '#limit_validation_errors' => array()
  );
  
  $form['cancel'] = array(
      '#type' => 'submit',
      '#value' => '<< Cancel >>',
      '#submit' => array('curation_tool_new_project_form_cancel'),
      '#limit_validation_errors' => array(),
  );
  
  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Submit',
      '#submit' => array('curation_tool_new_project_page_4_submit'),
      '#validate' => array('curation_tool_new_project_page_4_validate'),
  );
  return $form;
}

/**
 *
 * @param type $form
 * @param type $formstate 
 */
function curation_tool_new_project_page_4_validate($form, &$form_state) {
  $message = '';
  if(
          !$form_state['values']['assureCopyright'] || 
          !$form_state['values']['releaseCopyright'] || 
          !$form_state['values']['assureInformation']
          ) {
    $message = 'You must check each of the assurances before uploading and processing the data.';
  }
  
  form_set_error('assurances', $message);
}

/**
 * Handles submission for form 4 as wel as all submission data not previously
 * handled.
 * @todo redirect form based on response
 * @param type $form
 * @param type $form_state 
 */
function curation_tool_new_project_page_4_submit($form, &$form_state){
  // gather up the page values and combine them into the values index.
  foreach($form_state['page_values'] as $values) {
    $form_state['values'] = array_merge($form_state['values'], $values);
  } 
  
//  $postData = array();
//  $postData['repository'] = variable_get('data_curation_repository_location');
//  $postData['account'] = $form_state['values']['username']; 
//  $postData['project'] = $form_state['project']['name'];
//  $postData['xmlData'] = _get_header_meta($form_state);
//  $postData['descriptiveMetadata'] = _get_descriptive_meta($form_state);
  
  // Right now this just sends a blind request and assumes it works. This should
  // be fixed.
//  $request = new SimpleHTTPRequest();
//  $response = $request->set_url(variable_get('data_curation_processor_url'))
//          ->set_method('POST')
//          ->add_headers(array('content-type'=>'application/x-www-form-urlencoded'))
//          ->send_request();
  
}

/**
 * Helper function that just moves the data from the temporary directory
 * to its final destination.
 * @param array $form_state 
 */
function _handle_uploaded_data(&$form_state) {

  $userMessage = 'There was an error uploading your data. The geeks have been'.
        'notified and unleashed! Please try again '.
        'a few minutes. If the problem persists please '.
        '<a href = mailto:'.  variable_get('data_curation_help_email').
        'contact us </a>.';
  $file = $form_state['storage']['file'];
  // Get the location for the data.
  $destination = variable_get('data_curation_repository_location').'/'.$form_state['values']['username'];

  // If there is not directory for this user attempt to make one.
  if(!file_prepare_directory($destination, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS)) {
    if(!drupal_mkdir($destination, 0777)) {
      $adminMessage = 'Failed to create or prepare the directory "'.$destination/'"\n';

      _handle_error(
              'data][upload_data', 
              $userMessage, 
              $adminMessage, 
              __LINE__, 
              WATCHDOG_ERROR);
      }
  }
  
  $wrapper = implode('.', array_slice(explode('.', $file->filename), 0, -1));
  $form_state['project']['name'] = $wrapper;
  
  // Assume that the user knows what she is doing and delete any existing
  // directories and files.
  if(is_dir($destination.'/'.$wrapper)) {
    _rrmdir($destination.'/'.$wrapper);
  }

  // If the data is a zip file unpack it. Otherwise wrap a bare file.
  if(file_get_mimetype($destination.'/'.$file->filename) == 'application/zip') {
    $dirname = explode('.', $file->filename);
    $dirname = $dirname[0];

    if(!_unzip_file(variable_get('file_temporary_path').'/'.$file->filename, 
            $destination.'/'.$dirname)) {

      $adminMessage = 'Failed to unzip a file to "'.$destination/'"\n';

      _handle_error(
          'data][upload_data', 
          $userMessage, 
          $adminMessage, 
          __LINE__, 
          WATCHDOG_ERROR);

          }
  }
  else {
    drupal_mkdir($destination.'/'.$wrapper);
    if(
          !rename(
            variable_get('file_temporary_path').'/'.$file->filename, 
            $destination.'/'.$wrapper.'/'.$file->filename)
        ) {

      $adminMessage = 
            'Failed to move a file to "'.$destination.'"\n';

      _handle_error(
            'data][upload_data', 
            $userMessage, 
            $adminMessage, 
            __LINE__, 
            WATCHDOG_ERROR);
      }
    }
    
    $form_state['values'] = array();
    $form_state['storage']['file'] = NULL;
    file_delete($file, TRUE);
}


/**
 * Unzips a zip file.
 * @param type $fileLocation Location of the file
 * @param type $destination Destination folder for the zip file contents.
 */
function _unzip_file($fileLocation, $destination) {
  print "zip location: ".$fileLocation;
  print '<br/>';
  print "zip destiantion: ".$destination;
  $zip = new ZipArchive();
  $result = $zip->open($fileLocation);
  if($result == TRUE) {
    $zip->extractTo($destination);
    $zip->close();
  }
  
  return $result;
}

/**
 * Wraps a bare file in a directory of the same name sans extension
 * @param type $source
 * @param type $destination
 * @return type 
 */
function _wrap_file($source, $destination) {
      // Attempt to move the file to the correct location. Work around the 
    // "drupal way" to allow more flexibility in the final destination.
    return rename($source, $destination);
      $userMessage = 'There was an error uploading your data. The geeks have been '.
              'notified and unleashed! Please try again '.
              'a few minutes. If the problem persists please '.
              '<a href = mailto:'.  variable_get('data_curation_help_email').
              'contact us </a>.';

      $adminMessage = 
              'Failed to move a file to "'.$destination.'"\n';
      _handle_error(
              'data][upload_data', 
              $userMessage, 
              $adminMessage, 
              __LINE__, 
              WATCHDOG_ERROR);
}

/**
 *
 * @param type $formElement Form element name. NULL for none.
 * @param type $userMessage Message to display to the user.
 * @param type $adminMessage Message to include in the watchdog log and email to admin
 * @param type $line Line near which the error happened
 * @param type $watchdogError watchdog error, NULL for none.
 */
function _handle_error($formElement, $userMessage, $adminMessage, $line, $watchdogError = NULL) { 
    form_set_error($formElement, $userMessage);

    $body = 'DateTime: '.date('c', time()).'\n'.
              'Module: Curation tool(FILE: '.__FILE__.'; LINE: '.$line.')\n'.
              $adminMessage;
    mail(variable_get('site_mail'), 'DRUPAL ERROR', $body);
    watchdog('Curation Tool', $body, NULL, $watchdogError);
}

/**
 * Returns true if there is a project for the given owner with the given name,
 * false otherwise.
 * @param string $projectName
 * @param string $projectOwner
 * @return boolean 
 */
function _check_project_exisits($projectName, $projectOwner) {
  return is_dir(
          variable_get('data_curation_repository_location').'/'.
          $projectOwner.'/'.$projectName);
}

/**
 * Recursively remove directories.
 * @param string $dir 
 */
function _rrmdir($dir) {
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
}
?>
