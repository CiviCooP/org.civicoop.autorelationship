<?php

require_once 'autorelationship.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function autorelationship_civicrm_config(&$config) {
  _autorelationship_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function autorelationship_civicrm_xmlMenu(&$files) {
  _autorelationship_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function autorelationship_civicrm_install() {
  return _autorelationship_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function autorelationship_civicrm_uninstall() {
  return _autorelationship_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function autorelationship_civicrm_enable() {
  return _autorelationship_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function autorelationship_civicrm_disable() {
  return _autorelationship_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function autorelationship_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _autorelationship_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function autorelationship_civicrm_managed(&$entities) {
  return _autorelationship_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function autorelationship_civicrm_caseTypes(&$caseTypes) {
  _autorelationship_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function autorelationship_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _autorelationship_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementation of hook_civicrm_post
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_post
 */
function autorelationship_civicrm_post( $op, $objectName, $objectId, &$objectRef ) {
  if ($objectName == 'Address' && $objectRef instanceof CRM_Core_DAO_Address) {
    $matcher = new CRM_Autorelationship_CityMatcher(new CRM_Autorelationship_CityTarget(), $objectRef);
    $creator = new CRM_Autorelationship_Creator($matcher);
    $creator->matchAndCreate();
  }
}

/**
 * Implementatio of hook__civicrm_tabs
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_tabs
 */

function autorelationship_civicrm_tabs( &$tabs, $contactID ) { 
    // add a tab with the linked cities
    $url = CRM_Utils_System::url( 'civicrm/contact/tab/autorelationship_targetrules',
                                  "cid=$contactID&snippet=1" );
    
    //Count rules
    $factory = CRM_Autorelationship_TargetFactory::singleton();
    $count = $factory->getEntityListCount($contactID);
    
    $tabs[] = array( 'id'    => 'autorelationship_targetrules',
                     'url'   => $url,
                     'count' => $count,
                     'title' => ts('Automatic relationships'),
                     'weight' => 300 );
}

/**
 * Implementation of hook__civicrm_autorelationship_targetinterfaces
 * 
 * @param array $interfaces
 */
function autorelationship_autorelationship_targetinterfaces(&$interfaces) {
  $interfaces[] = new CRM_Autorelationship_CityTarget();
}