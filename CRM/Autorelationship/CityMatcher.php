<?php

/* 
 * This class find target ID's for the automatic relationship based on the postal codes
 */

class CRM_Autorelationship_CityMatcher extends CRM_Autorelationship_Matcher {
  
  protected $objAddress;
  
  /**
   * The ID of the custom field group 'Automatic Relationship'
   * 
   * @var int
   */
  protected $autogroup_id;
  
  /**
   * The ID of the address ID field on relationship, which is a custom field
   * 
   * @var int
   */
  protected $addressfield_id;
  
  /**
   * 
   * @param $objAddress
   */
  public function __construct($objAddress=null) {
    $this->objAddress = $objAddress;
    
    $this->autogroup_id = $this->getCustomGroupIdByName('autorelationship_city_based');
    $this->addressfield_id = $this->getCustomFieldIdByNameAndGroup('Address_ID', $this->autogroup_id);
  }
  
  public function getRelationshipTypeNameAB() {
    return 'city_based';
  }
  
  /**
   * Returns an array with the contact IDs which should have a relationship to the contact owner of the address
   * 
   * @param object $objAddress
   * @return array
   */
  public function findTargetContactIds() {
    if (!isset($this->objAddress)) {
      throw new Exception('Address not set');
    }    
    $objAddress = $this->objAddress;
    
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
  
  /**
   * Update the relationship parameters. E.g. for setting a custom field
   * 
   * @param type $arrRelationshipParams
   */
  public function updateRelationshipParameters(&$arrRelationshipParams) {
    if (!isset($this->objAddress)) {
      throw new Exception('Address not set');
    } 
    $arrRelationshipParams['custom_'.$this->addressfield_id] = $this->objAddress->id;
    //$arrRelationshipParams['return.custom_'.$this->addressfield_id] = 1;
  }
  
  /**
   * Returns the contact ID for on the A side of the relationship
   * 
   * @return int the contact ID for the A side of the relationship
   */
  public function getContactId() {
    if (!isset($this->objAddress) || !isset($this->objAddress->contact_id)) {
      throw new Exception('Address not set');
    } 
    return $this->objAddress->contact_id;
  }
  
  /**
   * Returns the id of a custom group, only relationship groups are checked
   * 
   * @param string $name
   * @return int
   */
  private function getCustomGroupIdByName($name) {
    $params['name'] = $name;
    $params['extends'] = 'Relationship';
    $result = civicrm_api3('CustomGroup', 'getsingle', $params);
    return $result['id'];
  }
  
  /**
   * Returns the ID of a custom field retrieved by its name and group_id
   * 
   * @param String $name
   * @param int $group_id
   * @return int
   */
  private function getCustomFieldIdByNameAndGroup($name, $group_id) {
    $params['custom_group_id'] = $group_id;
    $params['name'] = $name;
    $result = civicrm_api3('CustomField', 'getsingle', $params);
    return $result['id'];
  }
  
}
