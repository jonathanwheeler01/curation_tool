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
  
  $form['curator_info']['curator'][] = array(
      '#title' => t('Curator&apos;s Name'),
      '#description' => t('The name(s) of the individuals responsible for '.
              'curating this data set. Separate names with commas.'),
      '#type' => 'textfield',
      '#length' => 60,
      '#maxlength' => 120,
  );
  
  $form['curator_info']['date'] = array(
      '#title' => t('Creation Date'),
      '#type' => 'textfield',
      '#length' => 60,
      '#default_value' => date('c', time()),
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
      '#title' => t('Data Creators'),
      '#description' => t('Enter the names of other people with a role in creating the data '.
              'separated by commas.'),
      '#type' => 'textfield',
      '#length' => 80,
      '#maxlength' => 256,
  );
  
  $form['descriptive_metadata']['created'] = array(
      '#title' => t('Date Created'),
      '#description' => t('Enter the date the data was created <em>(YYYY-MM-DD)</em>'),
      '#type' => 'textfield',
      '#length' => 80,
      '#maxlength' => 256,
      '#default_value' => date('Y-m-d', time()),
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
  
  $form['action']['cancel'] = array(
      '#type' => 'submit',
      '#value' => 'Cancel',
  );
  
  $form['action']['next'] = array(
      '#type' => 'submit',
      '#value' => 'Submit',
  );
  
  return $form;
}

/**
 * Validate input here beyond what Drupal does.
 * @param array $form
 * @param array $form_state 
 */
function curation_tool_new_project_form_validate($form, &$form_state) {
  
}

function curation_tool_new_project_submit($form, &$form_state) {
  
}

?>
