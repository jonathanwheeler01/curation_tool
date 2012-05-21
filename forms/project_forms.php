<?php
/**
 * 
 * Implements a form to allow users to create a new dta project.
 * @author Robert Olendorf
 * @license Apache License 2.0
 *  
 */

function curation_tool_new_project_form($form, &$form_state) {

//  $form = array();
  $form['upload_data'] = array(
      '#type' => 'file',
      '#title' => t('Upload data'),
      '#description' => t('Upload a single file, or a zipped file for mutliple files.'),
      );
  
  $form['input'] = array(
      '#title' => t('test '),
      '#type' => 'textfield',
      '#required' => TRUE,
  );
  
  $form['submit'] = array(
      '#type' => 'submit',
      '#value' => 'submit',
  );
  
  return $form;
}
?>
