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
  if(empty($form_state['page'])) {
    $form_state['page'] = 1;
  }
  if(!empty($form_state['page']) && $form_state['page'] == 2) {
    return curation_tool_new_project_form_page_two($form, &$form_state);
  }
  if(!empty($form_state['page']) && $form_state['page'] == 3) {
    return curation_tool_new_project_form_page_three($form, &$form_state);
  }
  if(!empty($form_state['page']) && $form_state['page'] == 4) {
    return curation_tool_new_project_form_page_four($form, &$form_state);
  }
  
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
  
  while($name = $usernames->fetchAssoc()) {
    $userInfo = user_load_by_name($name);
    $firstName = field_get_items('user', $userInfo, 'field_first_name');
    $lastName = field_get_items('user', $userInfo, 'field_last_name');
    $options[] = $firstName[0]['value'].' '.$lastName[0]['value'].' - '.$name['name'];
  }
  
  if(empty($form_state['values']['username'])) {
    $firstName = field_get_items('user', $user, 'field_first_name');
    $lastName = field_get_items('user', $user, 'field_last_name');
    $default = $firstName[0]['value'].' '.$lastName[0]['value'].' - '.$user->name;
  }
  else {    
    $default = $form_state['values']['username'];
  }
  
  $form['data']['username'] = array(
      '#title' => 'Banner ID of the Project Owner',
      '#type' => 'select',
      '#default_value' => $default,
      '#access' => user_access('create any project'),
      '#required' => TRUE,
      '#options' => $options,
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

  if(empty($form_state['values']['curator'])) {
    $firstName = field_get_items('user', $user, 'field_first_name');
    $lastName = field_get_items('user', $user, 'field_last_name');
    $curator = $firstName[0]['value'].' '.$lastName[0]['value'];
  }
  else {
    $curator = $form_state['values']['curator'];
  }
  
  $form['curator_info']['curator'] = array(
      '#title' => t('Curator&apos;s Name(s)'),
      '#description' => t('The name(s) of the individuals responsible for '.
              'curating this data set. Separate names with commas.'),
      '#type' => 'textfield',
      '#length' => 60,
      '#maxlength' => 120,
      '#required' => TRUE,
      '#default_value' => $curator,
      );
  
  if(empty($form_state['values']['metaCreateDate'])) {
    $metaCreateDate = date('c', time());
  }
  else {
    $metaCreateDate = $form_state['values']['metaCreateDate'];
  }
  
  $form['curator_info']['metaCreateDate'] = array(
      '#title' => t('Creation Date'),
      '#type' => 'textfield',
      '#length' => 60,
      '#default_value' => $metaCreateDate,
      '#required' => TRUE,
  );
  
  $form['next'] = array(
      '#type' => 'submit',
      '#value' => 'Next >> ',
      '#submit' => array('curation_tool_new_project_form_next_submit'),
      '#validate' => array('curation_tool_new_project_form_page_one_validate'),
  );
  
  return $form;
}

function curation_tool_new_project_form_next_submit($form, &$form_state) {
  // Ensure that values for each page are carried forward. 
  $form_state['page_values'][1] = $form_state['values'];
 
  if (!empty($form_state['page_values'][2])) {
    $form_state['values'] = $form_state['page_values'][2];
  }
  
  $form_state['page']++;
  $form_state['rebuild'] = TRUE;
}


function curation_tool_new_project_form_back_submit($form, &$form_state) {
  $form_state['values'] = $form_state['page_values'][1];
  $form_state['page']--;
  $form_state['rebuild'] = TRUE;
}

function curation_tool_new_project_form_page_one_validate($form, &$form_state) {
  
}

function curation_tool_new_project_form_page_two($form, &$form_state) {
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
            array_key_exists('title', $form_state['values']) ? 
                                            $form_state['values']['title'] : '',
  );
  
  $form['descriptive_metadata']['creator'] = array(
      '#title' => t('Data Creators'),
      '#description' => t('Enter the names of the people responsible for creating the data '.
              'separated by commas.'),
      '#type' => 'textfield',
      '#length' => 80,
      '#maxlength' => 256,
      '#required' => TRUE,
      '#default_value' => 
            array_key_exists('creator', $form_state['values']) ? 
                                            $form_state['values']['creator'] : '',
  );
  
  $form['descriptive_metadata']['contributor'] = array(
      '#title' => t('Other Contributors'),
      '#description' => t('Enter the names of other people with a role in creating the data '.
              'separated by commas.'),
      '#type' => 'textfield',
      '#length' => 80,
      '#maxlength' => 256,
      '#default_value' => 
            array_key_exists('contributor', $form_state['values']) ? 
                                            $form_state['values']['contributor'] : '',
  );
  
  $form['descriptive_metadata']['subject'] = array(
      '#title' => t('Keywords'),
      '#description' => t('Enter keywords or phrases to aid in searching for your data separated by commas.'.
              'separated by commas.'),
      '#type' => 'textfield',
      '#length' => 80,
      '#maxlength' => 256,
      '#default_value' => 
            array_key_exists('subject', $form_state['values']) ? 
                                            $form_state['values']['subject'] : '',
  );
  
  $form['descriptive_metadata']['dataCreated'] = array(
      '#title' => t('Date Created'),
      '#description' => t('Enter the date the data was created <em>(YYYY-MM-DD)</em>'),
      '#type' => 'textfield',
      '#length' => 80,
      '#maxlength' => 256,
      '#default_value' => date('Y-m-d', time()),
      '#required' => TRUE,
      '#default_value' => 
            array_key_exists('dataCreated', $form_state['values']) ? 
                                            $form_state['values']['dataCreated'] : '',
  );
  
  $form['descriptive_metadata']['abstract'] = array(
      '#title' => t('Describe The Study'),
      '#description' => t('Provide a narrative description of the project.'),
      '#type' => 'textarea',
      '#cols' => 80,
      '#rows' => 10,
      '#resizeable' => TRUE,
      '#default_value' => 
            array_key_exists('abstract', $form_state['values']) ? 
                                            $form_state['values']['abstract'] : '',
  );
  
  $form['descriptive_metadata']['description'] = array(
      '#title' => t('Describe the Data'),
      '#description' => t('Provide a brief description of the structure of the data.'),
      '#type' => 'textarea',
      '#cols' => 80,
      '#rows' => 10,
      '#resizeable' => TRUE,
      '#default_value' => 
            array_key_exists('description', $form_state['values']) ? 
                                            $form_state['values']['description'] : '',
  );
  
  $form['back'] = array(
      '#type' => 'submit',
      '#value' => '<< Back',
      '#submit' => array('curation_tool_new_project_form_back_submit'),
      '#limit_validation_errors' => array(),
  );
  
  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Next >>',
      '#submit' => array('curation_tool_new_project_form_next_submit'),
      '#validate' => array('curation_tool_new_project_page_two_validate'),
  );
  return $form;
}

function curation_tool_new_project_page_two_validate($form, &$form_state) {
  
}

function curation_tool_new_project_form_page_three($form, &$form_state) {
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
  
  $form['back'] = array(
      '#type' => 'submit',
      '#value' => '<< Back',
      '#submit' => array('curation_tool_new_project_form_back_submit'),
      '#limit_validation_errors' => array()
  );
  
  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Next >>',
      '#submit' => array('curation_tool_new_project_form_next_submit'),
      '#validate' => array('curation_tool_new_project_page_three_validate'),
  );
  
  return $form;
}

function curation_tool_new_project_page_three_validate($form, &$form_state) {
  
}

function curation_tool_new_project_form_page_four($form, &$form_state) {
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
  
  $form['back'] = array(
      '#type' => 'submit',
      '#value' => '<< Back',
      '#submit' => array('curation_tool_new_project_form_back_submit'),
      '#limit_validation_errors' => array()
  );
  
  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'Submit',
      '#submit' => array('curation_tool_new_project_submit'),
      '#validate' => array('curation_tool_new_project_page_four_validate'),
  );
  return $form;
}

function curation_tool_new_project_page_four_validate($form, &$formstate) {
  
}

function curation_tool_new_project_submit($form, &$form_state) {
  
}

?>
