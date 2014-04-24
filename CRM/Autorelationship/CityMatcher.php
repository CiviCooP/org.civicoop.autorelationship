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
  public function __construct(CRM_Autorelationship_TargetInterface $interface) {
    parent::__construct($interface);    
    $this->autogroup_id = $this->getCustomGroupIdByName('autorelationship_city_based');
    $this->addressfield_id = $this->getCustomFieldIdByNameAndGroup('Address_ID', $this->autogroup_id);
  }
  
  protected function getRelationshipTypeNameAB() {
    return 'city_based';
  }
  
  public function setData($data) {
    parent::setData($data);
    if (isset($this->data['address'])) {
      $this->objAddress = $this->data['address'];
    }
  }
  
  /**
   * Returns an array with the contact IDs which should have a relationship to the contact based on the rule settings
   * array is build as follows:
   * [] = array (
   *  'contactId' => $contactId,
   *  'entity_id' => //Id of the target rule entity in the database
   *  'entity' => //System name of the entity
   * )
   * 
   * The 'entity_id' is the id of the rule in the database
   * 
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
      $target['contact_id'] = $dao->contact_id;
      $target['entity_id'] = $dao->id;
      $target['entity'] = $this->interface->getEntitySystemName();
      
      $return[] = $target;
    }
    
    return array_unique($return);
  }
  
  /**
   * Returns an array with all the contacts which should have a relationship based on the tule rule $entity_id
   * array is build as follows:
   * [] = array (
   *  'contactId' => $contactId //the source contactId 
   *  'entity_id' => //Id of the target rule entity in the database
   *  'entity' => //System name of the entity
   * )
   * 
   * @param $entity_id the ID of the rule in the database
   * @return array
   */
  public function findSourceContactIds($entity_id) {
    //retrieve the city value of the rule
    $sql = "SELECT * FROM `civicrm_autorelationship_contact_city` WHERE `id` = %1";
    $dao = CRM_Core_DAO::executeQuery($sql, array('1' => array($entity_id, 'Integer')));
    $city = false;
    if ($dao->fetch()) {
      $city = $dao->city;
    }
    
    //find all matching addresses with the city parameter
    $return = array();
    if ($city !== false) {  
      // find all primary addresses which city matches our city
      $sql = "SELECT * FROM `civicrm_address` WHERE `is_primary` = '1' AND LOWER(`city`) = LOWER(%1)";
      $dao = CRM_Core_DAO::executeQuery($sql, array(
        '1' => array($city, 'String')
      ));
      
      while($dao->fetch()) {
        $target['contact_id'] = $dao->contact_id;
        $target['entity_id'] = $entity_id;
        $target['entity'] = $this->interface->getEntitySystemName();
        
        $dataArray = $dao->toArray();
        $dataObject = json_decode(json_encode($dataArray), FALSE); //we need an object for the target data parameter
        
        $target['data']['address'] = $dataObject;
      
        $return[] = $target;
      }
    }
    return $return;
  }
  
  /**
   * Update the relationship parameters. E.g. for setting a custom field
   * 
   * @param type $arrRelationshipParams
   * @param array $target = array ( 'contact_id' => id, 'entity_id' => int, 'entity' => string)
   */
  public function updateRelationshipParameters(&$arrRelationshipParams, $target) {
    parent::updateRelationshipParameters($arrRelationshipParams, $target);
    
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
  
}
