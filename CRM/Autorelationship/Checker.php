<?php

/* Class to check the relationships with the target contact. 
 * 
 * This is handy when it you have edited the paramaters of the target contact 
 * and not all related contacts should be matched automaticly, or new ones should be added.
 * 
 */

class CRM_Autorelationship_Checker {
  
  protected $relationship_type_id;
  
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
   * @var CRM_Autorelationship_Matcher 
   */
  protected $matcher;
  
  public function __construct($relationship_type_name_a_b, CRM_Autorelationship_Matcher $matcher) {
    $this->loadRelationshipType($relationship_type_name_a_b);
    $this->autogroup_id = $this->getCustomGroupIdByName('Automatic_Relationship');
    $this->addressfield_id = $this->getCustomFieldIdByNameAndGroup('Address_ID', $this->autogroup_id);
    $this->matcher = $matcher;
  }
  
  //no clue...
  public function checkTarget($targetContactId) {
    //no clue what to do here....
  }
  
  /**
   * Set the relationship_type_id parameter by finding the right relationship based on the name_a_b
   * 
   * @param String $relationship_type_name_a_b
   */
  private function loadRelationshipType($relationship_type_name_a_b) {
    $params['name_a_b'] = $relationship_type_name_a_b;
    $result = civicrm_api3('RelationshipType', 'getsingle', $params);
    $this->relationship_type_id = $result['id'];
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