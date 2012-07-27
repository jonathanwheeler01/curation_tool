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
  if(empty($form_state['page'])) {
    $form_state['page'] = 1;
    
    $form_state['page_information'] = _new_project_wizard_pages();
  }
  
  $page = $form_state['page'];
  $form = $form_state['page_information'][$page]['form']($form, $form_state);
  drupal_set_message($form_state['page_information'][$page]['form']);
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
 * Page one of the wizard. Collects the basic project information.
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
  
  $form['data']['new_data'] = array(
      '#type'=> 'radios',
      '#options' => array(
          'new' => t('Upload New Data'),
          'existing' => t('Process Existing Data'),
      ),
      '#title' => t('Do you want to upload a new dataset or process an existing data set?'),
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
  return $form;
}
?>
