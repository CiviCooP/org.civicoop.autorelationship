<?php

/* 
 * This is factory class for targets for automatic relationships
 */

class CRM_Autorelationship_TargetFactory {
  
  /**
   * We only need one instance of this object. So we use the singleton
   * pattern and cache the instance in this variable
   *
   * @var object
   * @access private
   * @static
   */
  static private $_singleton = NULL;
  
  /**
   *
   * @var array 
   */
  private $interfaces;

  /**
   * class constructor
   */
  private function __construct() {
    $this->interfaces = $this->loadTargetInterfaces();
  }
  
  /**
   * Constructor and getter for the singleton instance
   *
   * @return instance of $config->userHookClass
   */
  static function singleton($fresh = FALSE) {
    if (self::$_singleton == NULL || $fresh) {
      self::$_singleton = new CRM_Autorelationship_TargetFactory();
    }
    return self::$_singleton;
  }
  
  /**
   * Returns an array with the target entities sorted by their weight
   * 
   * Format of the return array is
   * [0][] => array (
   *  'entity' => (String) System name of the entity
   *  'entity_label' => (String) Human name of the entity
   *  'entity_id' => (int) Id of the entity
   *  'label' => (String) The label of the entity
   *  'relationship_description' => (String) the description of the relationship
   * ),
   * [1][] => ...
   * 
   * @param int $targetContactId
   * @return array
   */
  public function getEntityList($targetContactId) {
    $return = array();
    foreach($this->interfaces as $interface) {
      $entities = $interface->listEntitiesForTarget($targetContactId);
      foreach($entities as $entity) {
        $e['label'] = $entity['label'];
        $e['entity_id'] = $entity['entity_id'];
        $e['entity'] = $interface->getEntitySystemName();
        $e['entity_label'] = $interface->getEntityHumanName();
        $e['relationship_description'] = $interface->getMatcher()->getRelationshipDescription();
        $weight = $entity['weight'];
        $id = $entity['entity_id'];
        
        $return[$weight][$id] = $e;
      }
    }
    
    return $return;
  }
  
  /**
   * Returns the number of automatic relationship rules
   * 
   * @param int $targetContactId
   * @return int
   */
  public function getEntityListCount($targetContactId) {
    $return = 0;
    foreach($this->interfaces as $interface) {
      $entities = $interface->listEntitiesForTarget($targetContactId);
      $return = $return + count($entities);
    }
    
    return $return;
  }
  
  /**
   * Deletes a specific target entity rule
   * 
   * @param String $entity
   * @param String $entityId
   * @param String $targetContactId
   */
  public function deleteEntity($entity, $entityId, $targetContactId) {
    $interface = $this->getInterfaceForEntity($entity);
    $interface->deleteTarget($entityId, $targetContactId);
  }
  
  /**
   * returns an array with the target interfaces
   */
  protected function loadTargetInterfaces() {
    $interfaces = array();
    $hooks = CRM_Utils_Hook::singleton();
    $hooks->invoke(1,
      $interfaces, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject, CRM_Utils_Hook::$_nullObject,
      'autorelationship_targetinterfaces'
      );
    return $interfaces;
  }
  
  /**
   * Returns an interface for a given entity
   * 
   * @param String $entity
   * @return CRM_Autorelationship_TargetInterface
   * @throws CRM_Core_Exception
   */
  public function getInterfaceForEntity($entity) {
    foreach($this->interfaces as $interface) {
      if ($interface->getEntitySystemName() == $entity) {
        return $interface;
      }
    }
    
    throw new CRM_Core_Exception('No valid entity type found');
  }
  
  /**
   * Return an array of all interfaces
   * 
   * @return array
   */
  public function getTargetInterfaces() {
    return $this->interfaces;
  }
  
  
}
