<?php

/*
 * interface for target entities
 * 
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @date 21 Feb 2014
 */

abstract class CRM_Autorelationship_TargetInterface {
  
  /**
   * Delete an target rule based target contactId and the id of the target entity
   * 
   * Should throw an exception on error
   * 
   * @param int $entityId
   * @param int $targetContactId
   * @return void
   * @throws CRM_Core_Exception
   */
  public abstract function deleteTarget($entityId, $targetContactId);
  
  /**
   * Returns an array (list) of entities for a specific target contact
   * 
   * The return array should contain the following format
   * [0] => array (
   *    'weight' => (int) (sortable weight)
   *    'label' => (String) (The label of target rule (e.g. for city it should be something like Amsterdam))
   *    'entity_id' => (int) (Id of the specific entity)
   * )
   * [1] => ...
   * 
   * @return array
   */
  public abstract function listEntitiesForTarget($targetContactId);
  
  /**
   * returns the name of the entity target
   * e.g. city or community
   * 
   * @return String
   */
  public abstract function getEntitySystemName();
  
  /**
   * returns a human readable (translatable) name for the entity
   * 
   * @return String
   */
  public abstract function getEntityHumanName();
  
  /**
   * returns the associated Matcher class
   * 
   * @return CRM_Autorelationship_Matcher
   */
  public abstract function getMatcher();
  
  /**
   * returns the url to the page for adding a new target rule for the contact
   * 
   * @return String
   */
  public abstract function getAddFormUrl();
  
}