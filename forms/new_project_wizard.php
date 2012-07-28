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
 * Description of new_project_wizard
 *
 * @author Robert Olendorf <olendorf@unm.edu>
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
  }
  
  $page = $form_state['page'];
  
  // The form rebuild forces the logic through this form. This statement
  // builds all of the form except submit buttons.
  $form = $form_state['page_information'][$page]['form']($form, $form_state);
  

  // All but the first page get a previous and cancel button.
  if($page > 1) {
    
    $form['previous'] = array(
        '#type' => 'submit',
        '#value' => t('<< Previous'),
        '#name' => 'previous',
        '#submit' => array('form_new_project_wizard_previous_submit'),
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
        '#value' => t('Finish'),
    );
  }
  
  // Perform any validation.
  if(function_exists($form_state['page_information'][$page]['form'].'_validate')) {
    $form['next']['#validate'] = array($form_state['page_information'][$page]['form'].'_validate');
  }
  
  
  return $form;
}

/**
 * The pages of the wizard, separated out for clarity.
 * @return type 
 */
function _new_project_wizard_pages() {
  return array(
      1 => array('form' => 'form_new_project_info',),
      2 => array('form' => 'form_new_project_descriptive_meta', ),
      3 => array('form' => 'form_new_project_rights_meta', ),
      4 => array('form' => 'form_new_project_assurances', ),
  );
}

/**
 *
 * @param type $form
 * @param type $form_state 
 */
function form_new_project_wizard_previous_submit($form, &$form_state) {
  $form_state['page_values'][$form_state['page']] = $form_state['values'];
  $form_state['page']--;
  $form_state['values'] = $form_state['page_values'][$form_state['page']];
  $form_state['rebuild'] = true;
}

/**
 *
 * @param type $form
 * @param type $form_state 
 */
function form_new_project_wizard_cancel_submit($form, &$form_state) {
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
  $form['data']['existing_data'] = array (
      '#type' => 'textfield',
      '#title' => t('Process an existing project'),
      '#description' => t('Enter the name of an existing project. '.
              '<br/><span class="warning">WARNING: This will erase any previous work done on this project.</span>'),
      '#states' => array(
          'visible' => array(':input[name="new_data"]' => array('value' => 'existing')),
      ), 
  );
  
  $form['curator_info'] = array(
      '#type' => 'fieldset',
      '#title' => t('Curator Info'),
      '#description' => t('Enter information about the curators and curatoin process here.'),
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
      '#type' => 'textfield',
      '#length' => 60,
      '#default_value' => key_exists(1, $form_state['page_values'])?
                                        $form_state['page_values'][1]['metaCreateDate']:date('c', time()),
      '#required' => TRUE,
      '#description' => t('Enter the date the project data was uploaded.'),
  );
  return $form;
}

/**
 *
 * @param type $form
 * @param type $form_state 
 */
function form_new_project_info_validate($form, &$form_state) {
  
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
      '#description' => t('Enter the date the data was created <em>(YYYY-MM-DD)</em>'),
      '#type' => 'textfield',
      '#length' => 80,
      '#maxlength' => 256,
      '#required' => TRUE,
      '#default_value' => 
            key_exists(2, $form_state['page_values'])?
                                $form_state['page_values'][2]['created']:date('Y-m-d', time()),
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
 * thinking about these things. We have not teeth to enforce it.
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
 *
 * @param type $form
 * @param type $form_state 
 */
function form_new_project_assurances_validate($form, &$form_state) {
  
}
?>
