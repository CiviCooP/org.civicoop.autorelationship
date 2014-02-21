<?php

/* 
 * interface for target city
 * 
 * This class is repsonible for linking cities to relationships
 * 
* @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
* @date 21 Feb 2014
 */

class CRM_Autorelationship_CityTarget extends CRM_Autorelationship_TargetInterface {
  
  public function getEntitySystemName() {
    return 'city';
  }
  
  public function getEntityHumanName() {
    return ts('City');
  }
  
  public function getMatcher() {
    return new CityMatcher();
  }
  
  public function listEntitiesForTarget($targetContactId) {
    $sql = "SELECT * FROM `civicrm_autorelationship_contact_city` WHERE `contact_id` = %1 ORDER BY `city`";
    $dao = CRM_Core_DAO::executeQuery($sql, array('1' => array($targetContactId, 'Integer')));

    $cities = array();
    $weight = 1;
    while ($dao->fetch()) {
      $city['entity_id'] = $dao->id;
      $city['label'] = $dao->city;
      $city['weight'] = $weight;
      $cities[] = $city;
      
      $weight++;
    }
    
    return $cities;
  }
  
  public function deleteTarget($entityId, $targetContactId) {
    $sql = "DELETE FROM `civicrm_autorelationship_contact_city` WHERE `id` = %2 AND `contact_id` = %1";
    $dao = CRM_Core_DAO::executeQuery($sql, array(
        '1' => array($targetContactId, 'Integer'),
        '2' => array($entityId, 'Integer')
      ));
  }
  
  public function getAddFormUrl() {
    return 'civicrm/autorelationship/addrule/city';
  }
  
}

