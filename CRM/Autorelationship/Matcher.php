<?php

/*
 * This is an interface for the matcher class. 
 * You can define and use your own Matchers if they are based on this class
 * 
 */

abstract class CRM_Autorelationship_Matcher {

  private $automatic_relationship_customgroup_id;
  private $automatic_relationship_targetrule_id;
  private $automatic_relationship_targetrule_entity;
  
  /**
   *
   * @var CRM_Autorelationship_TargetInterface 
   */
  protected $interface;
  
  /**
   * The data used for the matcher
   * 
   * @var array $data
   */
  protected $data;

  public function __construct(CRM_Autorelationship_TargetInterface $interface) {
    $this->interface = $interface;
    
    $this->automatic_relationship_customgroup_id = $this->getCustomGroupIdByName('automatic_relationship');
    $this->automatic_relationship_targetrule_id = $this->getCustomFieldIdByNameAndGroup('target_rule_id', $this->automatic_relationship_customgroup_id);
    $this->automatic_relationship_targetrule_entity = $this->getCustomFieldIdByNameAndGroup('target_rule_entity', $this->automatic_relationship_customgroup_id);
  }
  
  /**
   * Sets the data for the matcher
   * 
   * @param array $data
   */
  public function setData($data) {
    $this->data = $data;
  }

  /**
   *
   * @var array  
   */
  private $relationship_types;

  /**
   * Returns an array with the contact IDs which should have a relationship to the contact owner of the address
   * array is build as follows:
   * [] = array (
   *  'contactId' => $contactId,
   *  'entity_id' => //Id of the target rule entity in the database
   *  'entity' => //System name of the entity
   * )
   * 
   * The $rule_entity_id is the id of the rule in the database
   * 
   * @return array
   */
  abstract public function findTargetContactIds();

  /**
   * Returns the contact ID for on the A side of the relationship
   * 
   * @return int the contact ID for the A side of the relationship
   */
  abstract public function getContactId();

  /**
   * Returns the system name a-b of the relationship type
   * This name could then be used in further operations of creating relationships
   * 
   * @return String
   */
  abstract protected function getRelationshipTypeNameAB();

  //abstract protected function checkExistingRelationships($targetContactId);

  /**
   * Update the relationship parameters. E.g. for setting a custom field
   * 
   * @param type $arrRelationshipParams
   * @param array $target = array ( 'contact_id' => id, 'entity_id' => int, 'entity' => string)
   */
  public function updateRelationshipParameters(&$arrRelationshipParams, $target) {
    $arrRelationshipParams['custom_'.$this->automatic_relationship_targetrule_id] = $target['entity_id'];
    $arrRelationshipParams['custom_'.$this->automatic_relationship_targetrule_entity] = $target['entity'];
  }

  public function getRelationshipTypeId() {
    $relationshiptype = $this->getRelationshipType();
    if (!isset($relationshiptype['id'])) {
      throw new Exception("Invalid relationshiptype ");
    }
    return $relationshiptype['id'];
  }

  public function getRelationshipDescription() {
    $relationshiptype = $this->getRelationshipType();
    if (!isset($relationshiptype['description'])) {
      return '';
    }
    return $relationshiptype['description'];
  }

  private function getRelationshipType() {
    $name_a_b = $this->getRelationshipTypeNameAB();
    if (!isset($this->relationship_types[$name_a_b])) {
      $params['name_a_b'] = $name_a_b;
      $result = civicrm_api3('RelationshipType', 'getsingle', $params);
      $this->relationship_types[$name_a_b] = $result;
    }
    if (!isset($this->relationship_types[$name_a_b])) {
      throw new Exception("Invalid relationshiptype " . $name_a_b);
    }
    return $this->relationship_types[$name_a_b];
  }

  /**
   * Returns the id of a custom group, only relationship groups are checked
   * 
   * @param string $name
   * @return int
   */
  protected function getCustomGroupIdByName($name) {
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
  protected function getCustomFieldIdByNameAndGroup($name, $group_id) {
    $params['custom_group_id'] = $group_id;
    $params['name'] = $name;
    $result = civicrm_api3('CustomField', 'getsingle', $params);
    return $result['id'];
  }
  
  /**
   * Matches target contact ID's and updates, end or creates the relationships
   * 
   */
  public function matchAndCreate() {
    $creator = new CRM_Autorelationship_Creator($this);
    $creator->matchAndCreate();
  }

}
