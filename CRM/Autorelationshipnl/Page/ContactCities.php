<?php

require_once 'CRM/Core/Page.php';

class CRM_Autorelationshipnl_Page_ContactCities extends CRM_Core_Page {
  function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $this->assign('contactId', $this->_contactId);

    $sql = "SELECT * FROM `civicrm_autorelationshipnl_contact_city` WHERE `contact_id` = %1 ORDER BY `city`";
    $dao = CRM_Core_DAO::executeQuery($sql, array('1' => array($this->_contactId, 'Integer')));
    
    $cities = array();
    while($dao->fetch()) {
      $city['id'] = $dao->id;
      $city['city'] = $dao->city;
      $cities[$city['id']] = $city;
    }
    
    $this->assign('cities', $cities);
    
    parent::run();
  }
}
