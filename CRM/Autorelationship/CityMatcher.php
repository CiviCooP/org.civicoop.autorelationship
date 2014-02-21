<?php

/* 
 * This class find target ID's for the automatic relationship based on the postal codes
 */

class CRM_Autorelationship_CityMatcher extends CRM_Autorelationship_Matcher {
  
  public function __construct() {
    
  }
  
  public function getRelationshipType() {
    return 'city_based';
  }
  
  /**
   * Returns an array with the contact IDs which should have a relationship to the contact owner of the address
   * 
   * @param object $objAddress
   * @return array
   */
  public function findTargetContactIds($objAddress) {
    if ($objAddress->country_id != 1152) {
      return array(); //do not match if the country of the address is outside Netherlands
    }
    
    //do not match when address is not a primary address
    if ($objAddress->is_primary != '1') {
      return array();
    }

    $sql = "SELECT * FROM `civicrm_autorelationship_contact_city` WHERE LOWER(`city`) = LOWER('".$objAddress->city."')";
    
    $dao = CRM_Core_DAO::executeQuery($sql);
    $return = array();
    while($dao->fetch()) {
      $return[] = $dao->contact_id;
    }
    
    return array_unique($return);
  }
  
}
