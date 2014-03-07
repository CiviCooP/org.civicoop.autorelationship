<?php

/* 
 * The class below is responible for creating, updating and ending relationships based on criteria given in the matcher class
 * This class is repsonible for finding the targets.
 * 
 * Usage for example in the hook_civicrm_post on update of an address
 *  $creator = new CRM_Autorelationship_Creator(new YourOwnMatcher());
 *  $creator->
 */

class CRM_Autorelationship_Creator {

  protected $relationship_type_id;
  
  /**
   *
   * @var CRM_Autorelationship_Matcher 
   */
  protected $matcher;
  
  public function __construct(CRM_Autorelationship_Matcher $matcher) {
    $this->relationship_type_id = $matcher->getRelationshipTypeId();
    
    $this->matcher = $matcher;
  }
  
  /**
   * Matches target contact ID's and updates, end or creates the relationships
   * 
   * @param int $source_contact_id ID of the source contact
   */
  public function matchAndCreateForSourceContact($source_contact_id) {
    //do the matching
    $targets = $this->matcher->findTargetContactIds();
    
    $target_contact_ids = array();
    foreach($targets as $target) {
      $target_contact_ids[] = $target['contact_id'];
    }
    $contact_id = $source_contact_id;
    
    $this->endOldRelationships($contact_id, $target_contact_ids, $this->relationship_type_id);
    foreach($targets as $target) {
      $target_contact_id = $target['contact_id']; 
           
      /* check if a relationship exist */
      $existingId = $this->getExtistingRelationshipId($target, $contact_id, $this->relationship_type_id);
      
      if ($existingId === false) {
        $this->createNewRelationship($target, $contact_id, $this->relationship_type_id);
      } else {
        //relationship exist
        // Update it so it becomes active again
        $this->updateRelationship($existingId, $target_contact_id);
      }
    }
  }
  
  /**
   * Matches source contact ID's and or creates the relationships for the target contact and rule id
   * 
   * @param int $target_contact_id ID of the source contact
   * @param int $entityId ID of the rule to match
   */
  public function matchAndCreateForTargetContactAndEntityId($target_contact_id, $entityId) {
    //do the matching
    $source_contacts = $this->matcher->findSourceContactIds($entityId);
    //var_dump($$source_contacts); exit();
    foreach($source_contacts as $source) {
      $source_contact_id = $source['contact_id']; 
      $this->matcher->setData($source['data']);
      
      $target = $source;
      $target['contact_id'] = $target_contact_id;
      
      /* check if a relationship exist */
      $existingId = $this->getExtistingRelationshipId($target, $source_contact_id, $this->relationship_type_id);
      
      if ($existingId === false) {
        $this->createNewRelationship($target, $source_contact_id, $this->relationship_type_id);
      } else {
        //relationship exist
        // Update it so it becomes active again
        $this->updateRelationship($existingId, $target_contact_id);
      }
    }
  }
  
  /**
   * Update an existing relationship so it becomes active again.
   * 
   * @param int $existingId
   * @param int $target_contact_id
   */
  protected function updateRelationship($existingId, $target_contact_id) {
      $params['id'] = $existingId;
      $params['is_active'] = '1';
      try {
        civicrm_api3('Relationship', 'create', $params);
        
        $session = CRM_Core_Session::singleton();
        $session->setStatus(ts('Succesfully updated relationship'), '', 'info');
      } catch (Exception $ex) {
          //do nothing on error
      }
  }
  
  /**
   * retruns the id of an existing active relationship
   * returns false when none exist
   * 
   * @param array $target = array ( 'contact_id' => id, 'entity_id' => int, 'entity' => string)
   * @param int $target_contact_id
   * @param int $relationship_type_id
   */
  protected function getExtistingRelationshipId($target, $contact_id, $relationship_type_id) {    
    $id = false;
    
    $params['contact_id_b'] = $target['contact_id'];
    $params['contact_id_a'] = $contact_id;
    $params['relationship_type_id'] = $relationship_type_id;
    
    $this->matcher->updateRelationshipParameters($params, $target);
    
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
   * @param int $contact_id
   * @param array $target_contact_ids
   * @param int $relationship_type_id
   */
  protected function endOldRelationships($contact_id, $target_contact_ids, $relationship_type_id) {
    $params['relationship_type_id'] = $relationship_type_id;
    $params['contact_id_a'] = $contact_id;
    
    $this->matcher->updateRelationshipParameters($params, array());
    
    $result = civicrm_api3('Relationship', 'get', $params);
    if (isset($result['values']) && is_array($result['values'])) {
      foreach($result['values'] as $relationship) {
        //do not end relationship if it is one of the targets, only if the target doesn't exist anymore
        if (in_array($relationship['contact_id_b'], $target_contact_ids)) {
          continue;
        }
        
        $endDate = new \DateTime();
        $endParams['id'] = $relationship['id'];
        $endParams['end_date'] = $endDate->format('YmdHis'); //set end date for this relationship, so that it will be ended
        civicrm_api3('Relationship', 'Create', $endParams);
        
        $session = CRM_Core_Session::singleton();
        $session->setStatus(ts('Relationship ended'), '', 'info');
      }
    }
    
  }
  
  
  /**
   * 
   * @param array $target = array ( 'contact_id' => id, 'entity_id' => int, 'entity' => string)
   * @param int $target_contact_id The target contact for the relationship
   * @param int $relationship_type_id the id of the relationship type to create
   */
  protected function createNewRelationship($target, $contact_id, $relationship_type_id) {
    $relationship_params['contact_id_b'] = $target['contact_id'];
    $relationship_params['contact_id_a'] = $contact_id;
    $relationship_params['relationship_type_id'] = $relationship_type_id;
    $relationship_params['start_date'] = date('YmdHis');
    
    $this->matcher->updateRelationshipParameters($relationship_params, $target);
    
    try {
      civicrm_api3('Relationship', 'Create', $relationship_params);
      
      $session = CRM_Core_Session::singleton();
      $session->setStatus(ts('Created a new relationship'), '', 'info');
    } catch (Exception $e) {
      //do nothing on error. 
      Throw $e; //@Todo remove this statement
    }
  }
  
}