<?php
/**
 * Forms for administering the curation_tool interface.
 * 
 * @author Robert Olendorf
 * @license Apache License 2.0 
 */

/**
 * The form for configuring and administering the curation tool module.
 * @param type $form
 * @param type $form_state
 * @return array 
 */
function curation_tool_admin_form($form, &$form_state) {
  $form['data_curation_repository_root'] = array(
      '#type' => 'textfield',
      '#title' => t('Repository Root Directory'),
      '#default_value' => variable_get('data_curation_repository_location'),
      '#description' => t('The top level directory for all projects in the repository.'),
      '#size' => 60,
      '#maxlength' => 255,
      '#required' => TRUE,
  );
  
  $form['data_curation_help_email'] = array(
      '#type' => 'textfield',
      '#title' => t('Help Email'),
      '#default_value' => variable_get(
              'data_curation_help_email', 
              variable_get('site_mail')
              ),
      '#description' => t('An email address to use when a user needs help.'),
      '#size' => 60,
      '#maxlength' => 255,
      '#required' => TRUE,
  );
  
  $form['data_curation_processor_url'] = array(
      '#type' => 'textfield',
      '#title' => t('Data Curation URL'),
      '#default_value' => variable_get(
              'data_curation_processor_url', 
              variable_get('data_curation_processor_url')
              ),
      '#description' => t('The URL to the Data Curation middleware component.'),
      '#size' => 60,
      '#maxlength' => 255,
      '#required' => TRUE,
  );
  
  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save Settings'),
  );
  
  return $form;
}

function curation_tool_admin_form_validate($form, &$form_state) {
  if($form_state['values']['data_curation_repository_root'] == '') {
    form_set_error('data_curation_repository_root', t('The repository root must not be empty.'));
  }
  
  if(!is_dir($form_state['values']['data_curation_repository_root'])) {
    $values = array('@value' => $form_state['values']['data_curation_repository_root']);
    form_set_error('data_curation_repository_root', t('@value is not a valid directory.', $values));
  }
  
  if(!valid_email_address($form_state['values']['data_curation_help_email'])) {
    $values = array('@value' => $form_state['values']['data_curation_help_email']);
    form_set_error('data_curation_help_email', t('@value is not a valid email address.', $values));
  }
}

function curation_tool_admin_form_submit($form, &$form_state) {
  variable_set('data_curation_repository_location', $form_state['values']['data_curation_repository_root']);
  variable_set('data_curation_help_email', $form_state['values']['data_curation_help_email']);
  variable_set('data_curation_processor_url', $form_state['values']['data_curation_processor_url']);
  drupal_set_message(t('The settings have been save.'));
}
?>
