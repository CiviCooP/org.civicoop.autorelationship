<?php

/* 
 * This is an interface for the matcher class. 
 * You can define and use your own Matchers if they are based on this class
 * 
 */

abstract class CRM_Autorelationshipnl_Matcher {
  
  /**
   * Returns an array with the contact IDs which should have a relationship to the contact owner of the address
   * 
   * @param object $objAddress
   * @return array
   */
  abstract public function findTargetContactIds($objAddress);
  
}
