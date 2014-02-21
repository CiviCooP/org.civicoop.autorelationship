<?php

/* 
 * This is an interface for the matcher class. 
 * You can define and use your own Matchers if they are based on this class
 * 
 */

abstract class CRM_Autorelationship_Matcher {
  
  /**
   * Returns an array with the contact IDs which should have a relationship to the contact owner of the address
   * 
   * @param object $objAddress
   * @return array
   */
  abstract public function findTargetContactIds($objAddress);
  
  /**
   * Returns the system name a-b of the relationship type
   * This name could then be used in further operations of creating relationships
   * 
   * @return String
   */
  abstract public function getRelationshipType();
  
  /**
   * Returns an array with the contact IDs which should have a relationship to the contact owner of the address
   * 
   * This is the inverse action of the function findTargetContactIds
   * 
   * @param object $objContact
   * @return array
   */
  //abstract public function findSourceContactIds($objContact);
  
}
