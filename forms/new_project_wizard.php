<?php
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
 * A form wizard to help gather basic information about a data project prior
 * to upload and processing.
 *
 * @author Robert Olendorf <olendorf@unm.edu>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Aoache License, Version 2.0
 * 
 * NOTICE
 * This file has been modified from the original. In particular, the function
 * 'form_new_project_wizard_finish_submit' has been revised to make use
 * of Drupal's native HTTP request handling. Also, Drupal database calls have been added
 * to this function to handle inserting of project info to the 'curation_tool'
 * table.
 * 
 * Additional minor edits include a checkbox in the 'form_new_project_info'
 * function giving users the option to generate a single METS XML file per project
 * or else generate a METS XML file per project directory. Default is currently
 * set to generate a single file.
 * 
 * @todo Implement existing SimpleHTTP and Dublin Core wrapper methods.
 * 
 * Modifications authored by Jon Wheeler <jwheel01@unm.edu>
 * 
 */

/**
 * The primary form function
 * @param type $form
 * @param type $form 
 */
function form_new_project_wizard($form, &$form_state) {
  // Initialize the wizard if needed.
  // 
  if(empty($form_state['page'])) {
    $form_state['page'] = 1;
    $form_state['page_information'] = _new_project_wizard_pages();
    $form_state['page_values']=array();
    $form_state['values'] = array();
  }
  
  $page = $form_state['page'];
  
  // The form rebuild forces the logic through this form. This statement
  // builds all of the form except submit buttons.
  $form = $form_state['page_information'][$page]['form']($form, $form_state);
  
  // Sets the page title to show progress.
  drupal_set_title(
          t(
                  'New Project Wizard: @step. (Step @page of @pages)', 
                  array(
                      '@step' => $form_state['page_information'][$page]['name'],
                      '@page' => $page, 
                      '@pages' => sizeof($form_state['page_information']),
                      )
                  )
          );
  // All but the first page get a previous and cancel button.
  if($page > 1) {
    
    $form['previous'] = array(
        '#type' => 'submit',
        '#value' => t('<< Previous'),
        '#name' => 'previous',
        '#submit' => array('form_new_project_wizard_previous_submit'),
        '#limit_validation_errors' => array(),
        );
    
    $form['cancel']  = array(
        '#type' => 'submit',
        '#value' => t('<< Cancel >>'),
        '#name' => 'cancel',
        '#submit' => array('form_new_project_wizard_cancel_submit'),
        '#limit_validation_errors' => array(),
    );
  }
  
  // Add a Next>> button to all but the last pages.
  if($page < sizeof(_new_project_wizard_pages())) {
    $form['next'] = array(
        '#type' => 'submit',
        '#value' => t('Next >>'),
        '#name' => 'next',
        '#submit' => array('form_new_project_wizard_next_submit'),
    );
  }
  // Add a finish button to the last page.
  else {    
    $form['finish'] = array(
        '#type' => 'submit',
        '#name' => 'finish',
        '#value' => t('Finish'),
        '#submit' => array('form_new_project_wizard_finish_submit'),
    );
  }
  
  // Perform any validation.
  if(function_exists($form_state['page_information'][$page]['form'].'_validate')) {
    $form['next']['#validate'] = array($form_state['page_information'][$page]['form'].'_validate');
  }
//  print 'values for page '.$page.'<br/>';
//  var_dump($form_state['values']);
//  print 'page values values<br/>';
//  var_dump($form_state['page_values']);
  
  return $form;
}

/**
 * The pages of the wizard, separated out for clarity. New form pages must
 * have an array element here to be included in the wizard. The order of
 * the pages will be their order in the array. Each entry must
 * include with the following :
 * <dl>
 *  <dt>form: </dt>
 *    <dd>The name of the form function that creates the form.</dd>
 *  <dt>name: </dt>
 *    <dd> The form name to be displayed to the user. </dd>
 *  <dt>review: </dt>
 *    <dd> If this is set to true, data from the form page are displayed to the
 *    user at the final page.
 * </dl>
 * @return type 
 */
function _new_project_wizard_pages() {
  return array(
      1 => array('form' => 'form_new_project_info', 'name' => 'Project Information', 'review' => true),
      2 => array('form' => 'form_new_project_descriptive_meta','name' => 'Descriptive Information', 'review' => true ),
      3 => array('form' => 'form_new_project_rights_meta', 'name' => 'Rights Information', 'review' => true),
      4 => array('form' => 'form_new_project_assurances', 'name' => 'Assurances', 'review' => false),
      5 => array('form' => 'form_new_project_review', 'name' => 'Review', 'review' => false),
  );
}

/**
 * 
 * @param type $form
 * @param type $form_state 
 */
function form_new_project_wizard_previous_submit($form, &$form_state) {
  $form_state['page']--;
  $form_state['values'] = $form_state['page_values'][$form_state['page']];
  $form_state['rebuild'] = true;
}

/**
 *@todo Complete finish_submit function
 * @param type $form
 * @param type $form_state 
 */
function form_new_project_wizard_finish_submit($form, &$form_state) {
  foreach($form_state['page_values'] as $values) {
    $form_state['values'] = array_merge($form_state['values'], $values);
  }
  
  // drupal_set_message(t('Submitting values: @values', array('@values' => var_export($form_state['values'], TRUE))));
  
  $file = $form_state['storage']['file'];
  $wrapper = implode('.', array_slice(explode('.', $file->filename), 0, -1));
  
  // Update the curation_tool table in the db
  // First, get the UID of the current user
  global $user;
  $user_id = $user->uid;
  
  // Insert values into curation_tool table.
  $cur_tool = db_insert('curation_tool')->fields(array(
      'uid' => $user_id,
      'name' => $wrapper,
      'title' => $form_state['values']['title'],
      ))
      ->execute();
  
  // Get $form_state values for post data before passing to
  // _handle_uploaded_data, which resets $form_state to
  // an empty array.

  $postData = array();
  $postData['repository'] = variable_get('data_curation_repository_location');
  $postData['root'] = $form_state['values']['username'] . '/' . $wrapper;
  if ($form_state['values']['largeData'] == 1) {
      $postData['multiXML'] = 'yes';
  }
  else {
      $postData['multiXML'] = 'no';
  }
  $postData['creator'] = $form_state['values']['creator'];
  $postData['contributor'] = $form_state['values']['contributor'];
  $postData['title'] = $form_state['values']['title'];
  $postData['subject'] = $form_state['values']['subject'];
  $postData['description'] = $form_state['values']['description'];
  $postData['abstract'] = $form_state['values']['abstract'];
  //$postData['date'] = $form_state['values']['created'];
  $postData['rights'] = $form_state['values']['license'] . ' ' . 
          $form_state['values']['rights'];
  //$postData['account'] = $form_state['values']['username']; 
  //$postData['project'] = $form_state['project']['name'];
  //$postData['xmlData'] = _get_XFDUheader_meta($form_state);
  //$postData['descriptiveMetadata'] = _get_descriptive_meta($form_state);
  
  if($form_state['values']['new_data'] == 'new') {
    _handle_uploaded_data($form_state);
  }
  
  // Build the URL for post data.
  // @todo Implement existing SimpleHTTP methods, in
  // particular 'set_url'.

  $data = 'repository=' . $postData['repository'] . '&' . 'root=' . $postData['root'] . 
           '&' . 'multiXML=' . $postData['multiXML'] . '&' . 'creator=' . $postData['creator']
           . '&' . 'contributor=' . $postData['contributor'] . '&' . 'title=' . $postData['title']
           . '&' . 'subject=' . $postData['subject'] . '&' . 'description=' . $postData['description']
           . '&' . 'abstract=' . $postData['abstract']
           . '&' . 'rights=' . $postData['rights'];
  
  $options = array(
      'method' => 'POST',
      'data'=> $data,
      'headers' => array('Content-Type'=>'application/x-www-form-urlencoded'),
  );
  
  $result = drupal_http_request(variable_get('data_curation_processor_url'), $options);
}

/**
 * Handles cancelling a submission process. Clears any form values and
 * starts from page one.
 * @param type $form
 * @param type $form_state 
 */
function form_new_project_wizard_cancel_submit($form, &$form_state) {
  if(isset($form_state['storage']['file'])) {
    file_delete($form_state['storage']['file'], TRUE);
  }
  $form_state['page'] = 1;
  $form_state['values'] = array();
  $form_state['page_values'] = array();
  $form_state['rebuild'] = true;
}

/**
 * Handle creating the next page.
 * @param type $form
 * @param type $form_state
 * @return type 
 */
function form_new_project_wizard_next_submit($form, &$form_state) {
  // Stash the pages values into the page_value key
  $form_state['page_values'][$form_state['page']] = $form_state['values'];
  if($form_state['page'] < sizeof(_new_project_wizard_pages())) {
    $form_state['page']++;
    
    // If the next page has values place them in current page values.
    if(!empty($form_state['page_values'][$form_state['page']])) {
      $form_state['values'] = $form_state['page_values'][$form_state['page']];
    }
    else {
      $form_state['values'] = array();
    }
    // Force rebuild for all but the last page.
    $form_state['rebuild'] = true;
    return;
  }
}

/**
 * Collects the basic project information.
 * @todo add ajax support that creates a drop-down box based on a users existing
 * projects and updates wihen the project drop-down changes.
 * 
 * @todo consider adding the data module to make the dates even nicer. low priority.
 * 
 * @param type $form
 * @param type $form_state 
 */
function form_new_project_info($form, &$form_state) {
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
    $dataowner = $user->name;
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
  
  $form['data']['new_data'] = array(
      '#type'=> 'radios',
      '#options' => array(
          'new' => t('Upload New Data'),
          'existing' => t('Process Existing Data'),
      ),
      '#title' => t('Do you want to upload a new dataset or process an existing data set?'),
  );
  
  // Only display this element if the "new" radio button is selected.
  $form['data']['upload_data'] = array(
      '#name' => 'files[upload_data]',
      '#type' => 'file',
      '#title' => t('Upload and process new data'),
      '#description' => t('Upload a single file, or a zipped file for mutliple files.'),
      '#states' => array(
          'visible' => array(':input[name="new_data"]' => array('value' => 'new')),
      ),
      );
  
  // Only display this element if the "existing" radio button is selected.
  /**
   *@todo Use AJAX to collect the users current projects into a drop down list.
   * Server should return an array, which is turned into HTML by the javascript. 
   */
  $form['data']['existing_data'] = array (
      '#type' => 'textfield',
      '#title' => t('Process an existing project'),
      '#description' => t('Enter the name of an existing project. '.
              '<br/><span class="warning">WARNING: This will erase any previous work done on this project.</span>'),
      '#states' => array(
          'visible' => array(':input[name="new_data"]' => array('value' => 'existing')),
      ), 
  );
  
  // Provide an option to generate a single METS XML file for the project,
  // rather than one per project directory.
  
  $form['data']['largeData'] = array(
      '#type' => 'checkbox',
      '#description' => 'Check here to generate multiple METS XML files for a large data set.',
  );
  
  $form['curator_info'] = array(
      '#type' => 'fieldset',
      '#title' => t('Curator Info'),
      '#description' => t('Enter information about the curators and curation process here.'),
  );
  
  $form['curator_info']['curator'] = array(
      '#title' => t('Curator&apos;s Name(s)'),
      '#description' => t('The name(s) of the individuals responsible for '.
              'curating this data set. Separate names with vertical bars (|).'),
      '#type' => 'textfield',
      '#length' => 60,
      '#maxlength' => 120,
      '#required' => TRUE,
      '#default_value' => key_exists(1, $form_state['page_values'])?
                                        $form_state['page_values'][1]['curator']:
                                        $firstName[0]['value'].' '.$lastName[0]['value'],
      );
  
  $form['curator_info']['metaCreateDate'] = array(
      '#title' => t('Creation Date'),
      '#type' => 'date',
      '#default_value' => key_exists(1, $form_state['page_values'])?
                                        $form_state['page_values'][1]['metaCreateDate']:null,
      '#required' => TRUE,
      '#description' => t('Enter the date the project data was uploaded.'),
  );
  return $form;
}

/**
 * Validate data from the project info form.
 * @param type $form
 * @param type $form_state 
 */
function form_new_project_info_validate($form, &$form_state) {
        
  global $user;
  $user = user_load($user->uid);
  
  // If the user is uploading new data, validate the file attempt to upload it.
  if($form_state['values']['new_data'] == 'new') {
      // Attempt to validate the file and upload it.
      $validators = array(
          'file_validate_size' => array(file_upload_max_size()),
          'file_validate_extensions' => array(),
          );
      $file = file_save_upload('upload_data', $validators);
      
      $safefilename = filter_var($file->filename, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH);
      $safefilename = str_replace(' ', '', $safefilename);
      $safefilename = str_replace(',', '', $safefilename);
      
      if($safefilename != $file->filename) {
        $message = 'Your file name was changed.  Special characters and/or spaces '.
                'were removed for better web access.';
        drupal_set_message($message, 'warning');
      }
      
      rename(
              variable_get('file_temporary_path').'/'.$file->filename, 
              variable_get('file_temporary_path').'/'.$safefilename);
      
      $file->filename = $safefilename;
      if($file) {
      // Avoid accidentally overwriting existing projects.
    
        $dirName = str_replace(' ', '', implode('.', array_slice(explode('.', $file->filename), 0, -1)));
        
        // Only allow users with the appropriate permissions to overwrite a 
        // previous project.
        if(_check_project_exists($dirName, $form_state['values']['username'])) {
          if(
                  user_access('delete any project') || 
                          (user_access('delete own project') && 
                           $user->name == $form_state['values']['username']
                          )
                  ) {
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
            file_delete($file, TRUE);
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
  else if($form_state['values']['new_data'] == 'existing') {
    if($form_state['values']['existing_data'] == '') {
      $message = 'You must specify an existing data set.';
      form_set_error('data][existing_data', $message);
    }
    else if(!_check_project_exisits($form_state['values']['existing_data'], $user->name)) {
      $message = 'You must specify an existing data set.';
      form_set_error('data][existing_data', $message);
    }
  }
  else {
    $message = 'You must select either "Upload New Data" or "Process Existing Data"';
    form_set_error('new_data', $message);
  }
}

/**
 * Gathers descriptive metadata for the project.
 * @param type $form
 * @param type $form_state
 * @return type 
 */
function form_new_project_descriptive_meta($form, &$form_state) {
  
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
      '#description' => t('Enter the date the data was created.'),
      '#type' => 'date',
//      '#length' => 80,
//      '#maxlength' => 256,
      '#required' => TRUE,
      '#default_value' => 
            key_exists(2, $form_state['page_values'])?
                                $form_state['page_values'][2]['created']:null,
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
  return $form;
}

/**
 *
 * @param type $form
 * @param type $form_state 
 */
function form_new_project_descriptive_meta_validate($form, &$form_state) {

}

/**
 * Collect metadata pertaining to rights associated with the metadata.
 * @param type $form
 * @param type $form_state
 * @return type 
 */
function form_new_project_rights_meta ($form, &$form_state) {
  $form['license_metadata'] = array(
      '#type' => 'fieldset',
      '#title' => 'Rights and Licensing Information',
      '#description' => 'Use this section to document licensing and rights issues '.
              'concerning your entire dataset. If only '.
              'parts of your dataset can be claimed, you may document that specifically '.
              'for those files or groups of file later.',
  );
  

    $defaultLicense = 'This work is licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License.';

  
  $form['license_metadata']['license'] = array(
      '#title' => t('Licensing Information'),
      '#description' => t('Describe how you want your data licensed. <br/><strong>NOTE: '.
              'Your work must have an open license to be included in the repository.</strong>'),
      '#type' => 'textarea',
      '#cols' => 80,
      '#rows' => 10,
      '#default_value' => 
            key_exists(3, $form_state['page_values'])?
                                $form_state['page_values'][3]['license']:$defaultLicense,
      '#resizeable' => TRUE,
  );
  
  $form['license_metadata']['rights'] = array(
      '#title' => t('Rights Information'),
      '#description' => t('If any other parties can claim rights over this data set, list them and describe the nautre of their claim.' ),
      '#type' => 'textarea',
      '#cols' => 80,
      '#rows' => 10,
      '#resizeable' => TRUE,
      '#default_value' => 
            key_exists(3, $form_state['page_values'])?
                                $form_state['page_values'][3]['rights']:'',
  );
  return $form;
}

/**
 *
 * @param type $form
 * @param type $form_state 
 */
function form_new_project_rights_meta_validate($form, &$form_state) {
  
}

/**
 * Just have the user click the boxes to put them on notice they should be
 * thinking about these things. We have not teeth to enforce it. Since
 * validation won&amp;t allow progress beyond this page without all boxes
 * being checked, this should be sufficient. The values are not currently
 * stored anywhere.
 * @param type $form
 * @param type $form_state
 * @return string 
 */
function form_new_project_assurances($form, &$form_state) {
  
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
  
  return $form;
}

/**
 * Prevents user from moving beyond this step until all boxes are checked.
 * @param type $form
 * @param type $form_state 
 */
function form_new_project_assurances_validate($form, &$form_state) {
  $message = "You must acknowledge each assurance by checking the box.";
  if(
          !$form_state['values']['assureCopyright'] || 
          !$form_state['values']['releaseCopyright'] || 
          !$form_state['values']['assureInformation']
          ) {
    form_set_error('assurances', $message);
  }
}

function form_new_project_review($form, &$form_state) {
  //var_dump($form_state['page_values']);
    $form['markup'] = array(
        '#type' => 'markup',
        '#markup' => _format_data($form_state),
    );
    return $form;
}

/**
 * Utility function to convert a data into a datetime. METs tends to require
 * full xsd datetime format, but thats annoying in forms and not really 
 * necessary for most application. Just appends the datetime to be midnigt of
 * that day.
 * @param type $date
 * @param DateTimeZone $timezone
 * @return type 
 */
function _format_date_to_xsd_datetime($date, DateTimeZone $timezone = null) {
// Use system timezone if none is supplied.

  if(empty($timezone)) {
    $timezone = new DateTimeZone(date_default_timezone_get());
    } 


  $datetime = new DateTime('now', $timezone);
  $offset = $datetime->getOffset();
  $hours = abs(round($offset/3600));
  $mintues = ($offset%3600)/60;
  $offsetString = 'T00:00:00';

  // Handle Greenwich mean time.
  if($offset == 0) {
    return $date.$offsetString.'Z';
  }

  // Handle the sign
  if($offset > 0) {
    $offsetString.='+';
  }
  else {
    $offsetString.='-';
  }

  // Ensure leading zero for hourse
  if($hours < 10) {
    $offsetString.='0'.$hours.':';
  }
  else {
    $offsetString.=$hours.':';
  }

  // Ensure leadning zero for minutes
  if($mintues < 10 ) {
    $offsetString.='0'.$mintues;
  }
  else {
    $offsetString.=$mintues;
  }

  return $date.$offsetString;
}

/**
 * Returns true if there is a project for the given owner with the given name,
 * false otherwise.
 * @param string $projectName
 * @param string $projectOwner
 * @return boolean 
 */
function _check_project_exists($projectName, $projectOwner) {
  return is_dir(
          variable_get('data_curation_repository_location').'/'.
          $projectOwner.'/'.$projectName);
}

function _format_data($form_state) {
  
  $output = theme('html_tag', array(
          'element' => array(
              '#tag' => 'p',
              '#value' => 'Please review your information. If it is correct '.
                          'click <strong>Finish</strong>. You can use the '.
                          '<strong>Previous</strong> and <strong>Next '.
                          '</strong> buttons to revisit pages and correct any'.
                          'errors.',
           ),
      ));
  
  $pageInfo = _new_project_wizard_pages();
  
  for($i = 0; $i < sizeof($pageInfo); $i++) {
    if($pageInfo[$i+1]['review']) {
      $output .= '<hr/>'.theme('html_tag', array(
          'element' => array(
              '#tag' => 'h2',
              '#value' => $pageInfo[$i+1]['name'],
          ),
      ));
      
      // These keys come in the page values but we don't really want to see them.
      $ignoreFields = array('form_build_id', 'form_token', 'form_id', 'cancel', 'next',
          'previous', 'new_data', 'username', 'upload_data', 'existing_data');
      
      // Filter out the fields we know shouldn't be included.
      $data = array_diff_key(
                $form_state['page_values'][$i+1], 
                array_flip($ignoreFields)
              );
      
      $items = array();
      if($pageInfo[$i+1]['name'] == 'Project Information') {
        if($form_state['page_values'][$i+1]['new_data'] == 'new') {
          $items[] = '<strong>action: </strong>new project';
          $items[] = '<strong>file: </strong>'.$form_state['storage']['file']->filename;
        }
        else {
          $items[] = '<strong>action: </strong>existing project';
          $items[] = '<strong>project: </strong>'.$form_state['page_values'][$i+1]['existing_data'];
        }
      }
      foreach($data as $field => $value) {
        if(is_array($value)) {
          $value = $value['year'].'-'.$value['month'].'-'.$value['day'];
        }
        $items[] = '<strong>'.$field.': </strong>'.$value;
      }
      $output .= theme('item_list', array('items'=>$items, 'type'=>'ul'));
    }
  }
  
  $output = theme('container', array(
      'element' => array(
          '#attributes' => array(
              'class' => 'review',
          ),
          '#children' => $output,
      ),
  ));
  return $output;
}
/**
 * Helper function that just moves the data from the temporary directory
 * to its final destination.
 * @param array $form_state 
 */
function _handle_uploaded_data(&$form_state) {

  $userMessage = 'There was an error uploading your data. The geeks have been'.
        ' notified and unleashed! Please try again '.
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

  // If the data is a zip file unpack it. Otherwise wrap a bare file.
  if(file_get_mimetype($destination.'/'.$file->filename) == 'application/zip') {
    $dirname = explode('.', $file->filename);
    $dirname = $dirname[0];
    $form_state['project']['name'] = $destination. '/'.$dirname;

    if(!_unzip_file(variable_get('file_temporary_path').'/'.$file->filename, 
            $destination)) {

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
    
  
  $wrapper = implode('.', array_slice(explode('.', $file->filename), 0, -1));
  $form_state['project']['name'] = $wrapper;
  
  // Assume that the user knows what she is doing and delete any existing
  // directories and files.
  if(is_dir($destination.'/'.$wrapper)) {
    _rrmdir($destination.'/'.$wrapper);
  }
  
  if(!drupal_mkdir($destination.'/'.$wrapper, null, true)) {
      _handle_error(
              'data][upload_data', 
              $userMessage, 
              $adminMessage, 
              __LINE__, 
              WATCHDOG_ERROR
              );
    }
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
 * Recursively remove directories.
 * @param string $dir 
 */
function _rrmdir($dir) {
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file))
            rmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
}


/**
 * Unzips a zip file.
 * @param type $fileLocation Location of the file
 * @param type $destination Destination folder for the zip file contents.
 */
function _unzip_file($fileLocation, $destination) {
  print "zip location: ".$fileLocation;
  print '<br/>';
  print "zip destination: ".$destination;
  $zip = new ZipArchive();
  $result = $zip->open($fileLocation);
  if($result == TRUE) {
    $zip->extractTo($destination);
    $zip->close();
  }
  
  return $result;
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

/**
 * Gets the header meta for an xfdu file
 * @param type $form_state
 * @return type 
 */
function _get_XFDUheader_meta(&$form_state) {
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

