<?php

/* 
 * This is an interface for the matcher class. 
 * You can define and use your own Matchers if they are based on this class
 * 
 */

abstract class CRM_Autorelationship_Matcher {
  
  /**
   *
   * @var array  
   */
  private $relationship_types; 
  
  /**
   * Returns an array with the contact IDs which should have a relationship to the contact owner of the address
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
   */
  public function updateRelationshipParameters(&$arrRelationshipParams) {
    
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
      throw new Exception("Invalid relationshiptype ".$name_a_b);
    }
    return $this->relationship_types[$name_a_b];
  }
}
