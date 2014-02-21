<?php

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Autorelationship_Form_AddCityTargetRule extends CRM_Autorelationship_Form_AddTargetRule {
  
  function buildQuickForm() {

    // add form elements
    $this->add(
      'text', // field type
      'city', // field name
      ts('City'), // field label
      '', // list of options
      true // is required
    );
    
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    parent::buildQuickForm();
  }

  function postProcess() {
    //add city to database
    $city = $this->exportValue('city');
    
    //check if city already exist
    $exist = false;    
    $sql = "SELECT * FROM `civicrm_autorelationship_contact_city` WHERE LOWER(`city`) = LOWER(%1) AND `contact_id` = %2";
    $dao = CRM_Core_DAO::executeQuery($sql, array(
        '1' => array($city, 'String'),
        '2' => array($this->targetContactId, 'Integer')
    ));
    if ($dao->fetch()) {
      $exist = true;
    }
    
    if (!$exist) {
      $sql = "INSERT INTO `civicrm_autorelationship_contact_city` (`city`, `contact_id`) VALUES(%1, %2)";
      $dao = CRM_Core_DAO::executeQuery($sql, array(
        '1' => array($city, 'String'),
        '2' => array($this->targetContactId, 'Integer')
      ));
    }
    
    parent::postProcess();
  }
  
}
