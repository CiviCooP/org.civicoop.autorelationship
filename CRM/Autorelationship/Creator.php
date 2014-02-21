<?php

/* 
 * The class below is responible for creating, updating and ending relationships based on dutch postal codes.
 */

class CRM_Autorelationship_Creator {

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
  
  /**
   * Matches target contact ID's and updates, end or creates the relationships
   * 
   * @param type $objAddress The address which is used as a base for matching
   */
  public function matchAndCreate($objAddress) {
    //do the matching
    $target_contact_ids = $this->matcher->findTargetContactIds($objAddress);
    $this->endOldRelationships($objAddress, $target_contact_ids, $this->relationship_type_id);
    foreach($target_contact_ids as $target_contact_id) {
           
      /* check if a relationship exist */
      $existingId = $this->getExtistingRelationshipId($objAddress, $target_contact_id, $this->relationship_type_id);
      
      if ($existingId === false) {
        $this->createNewRelationship($objAddress, $target_contact_id, $this->relationship_type_id);
      } else {
        //relationship exist
        // Update it so it becomes active again
        $this->updateRelationship($existingId, $objAddress, $target_contact_id);
      }
    }
  }
  
  /**
   * Returns an array with the matched target contact ids.
   * 
   * @param object $objAddress
   */
  protected function findTargetContactIds($objAddress) {
    return array();
  }
  
  /**
   * Update an existing relationship so it becomes active again.
   * 
   * @param int $existingId
   * @param object $objAddress
   * @param int $target_contact_id
   */
  protected function updateRelationship($existingId, $objAddress, $target_contact_id) {
      $params['id'] = $existingId;
      $params['is_active'] = '1';
      try {
        civicrm_api3('Relationship', 'create', $params);
      } catch (Exception $ex) {
          //do nothing on error
      }
  }
  
  /**
   * retruns the id of an existing active relationship
   * returns false when none exist
   * 
   * @param object $objAddress
   * @param int $target_contact_id
   * @param int $relationship_type_id
   */
  protected function getExtistingRelationshipId($objAddress, $target_contact_id, $relationship_type_id) {    
    $id = false;
    
    $params['contact_id_a'] = $objAddress->contact_id;
    $params['contact_id_b'] = $target_contact_id;
    $params['relationship_type_id'] = $relationship_type_id;
    $params['custom_'.$this->addressfield_id] = $objAddress->id;
    
    $result = civicrm_api3('Relationship', 'get', $params);
    if (isset($result['values']) && is_array($result['values'])) {
      foreach($result['values'] as $relationship) {
        if (isset($relationship['end_date']) && strlen($relationship['end_date'])) {
          continue; //this is an ended relationship
        }
        
        /* Save the id of current relationship 
         * if this one is active quit the function and return that ID
         * if this one is not active save and loop till we find an active one
         * or return it when we don't find an active one at all.
         */
        $id = $relationship['id'];
        if (isset($relationship['is_active']) && $relationship['is_active'] == '1') {
          return $id;
        }
      }
    }
    
    return $id;
  }
  
  /**
   * End all automatic relationships who are no longer a target anymore.
   * @param object $objAddress
   * @param array $target_contact_ids
   * @param int $relationship_type_id
   */
  protected function endOldRelationships($objAddress, $target_contact_ids, $relationship_type_id) {
    $params['relationship_type_id'] = $relationship_type_id;
    $params['contact_id_a'] = $objAddress->contact_id;
    $params['return.custom_'.$this->addressfield_id] = 1;
    $params['custom_'.$this->addressfield_id] = $objAddress->id;
    $result = civicrm_api3('Relationship', 'get', $params);
    if (isset($result['values']) && is_array($result['values'])) {
      foreach($result['values'] as $relationship) {
        //do not end relationship if it is one of the targets, only if the target doesn't exist anymore
        if (in_array($relationship['contact_id_b'], $target_contact_ids)) {
          continue;
        }
        
        $addressEndDate = new \DateTime();
        $endParams['id'] = $relationship['id'];
        $endParams['end_date'] = $addressEndDate->format('YmdHis'); //set end date for this relationship, so that it will be ended
        civicrm_api3('Relationship', 'Create', $endParams);
      }
    }
    
  }
  
  
  /**
   * 
   * @param object $objAddress - The address which is used as a base for matching
   * @param int $target_contact_id The target contact for the relationship
   * @param int $relationship_type_id the id of the relationship type to create
   */
  protected function createNewRelationship($objAddress, $target_contact_id, $relationship_type_id) {
    //var_dump($objAddress); exit();
    $relationship_params['contact_id_a'] = $objAddress->contact_id;
    $relationship_params['contact_id_b'] = $target_contact_id;
    $relationship_params['relationship_type_id'] = $relationship_type_id;
    $relationship_params['start_date'] = date('YmdHis');
    $relationship_params['custom_'.$this->addressfield_id] = $objAddress->id;
    try {
      civicrm_api3('Relationship', 'Create', $relationship_params);
    } catch (Exception $e) {
      //do nothing on error. 
      Throw $e; //@Todo remove this statement
    }
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