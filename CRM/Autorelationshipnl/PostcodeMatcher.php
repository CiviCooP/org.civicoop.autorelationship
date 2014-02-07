<?php

/* 
 * This class find target ID's for the automatic relationship based on the postal codes
 */

class CRM_Autorelationshipnl_PostcodeMatcher extends CRM_Autorelationshipnl_Matcher {
  
  public function __construct() {
    
  }
  
  /**
   * Returns an array with the contact IDs which should have a relationship to the contact owner of the address
   * 
   * @param object $objAddress
   * @return array
   */
  public function findTargetContactIds($objAddress) {
    if ($objAddress->country_id != 1152) {
      return array(); //do not match if the country of the address is outside Netherlands
    }
    
    //do not match when address is not a primary address
    if ($objAddress->is_primary != '1') {
      return array();
    }
    
    $group_id = $this->getCustomGroupIdByName('Postcodes');
    $table = $this->getCustomGroupTableById($group_id);
    $van_postcode_num = $this->getCustomFieldColumnByNameAndGroup('Van_postcode_cijfers_', $group_id);
    $tot_postcode_num = $this->getCustomFieldColumnByNameAndGroup('T_m_postcode_cijfers_', $group_id);
    $van_postcode_letter = $this->getCustomFieldColumnByNameAndGroup('Van_postcode_letters_', $group_id);
    $tot_postcode_letter = $this->getCustomFieldColumnByNameAndGroup('T_m_postcode_letters_', $group_id);
    
    $postcode_cijfer = false;
    $postcode_letter = false;
    
    $postcode = preg_replace('/[^\da-z]/i', '', $objAddress->postal_code);
    $postcode_4pp = substr($postcode, 0, 4); //select the four digist
    $postcode_2pp = substr($postcode, 4, 2); //select the 2 letters
    if (strlen($postcode_4pp) == 4) {
      $postcode_cijfer = $postcode_4pp;
    }
    if (strlen($postcode_2pp) == 2) {
      $postcode_letter = strtoupper($postcode_2pp);
    }
    
    if ($postcode_cijfer === false || $postcode_letter === false) {
      return array(); //do not match any because there is no valid postal code
    }

    $sql = "SELECT * FROM `".$table."` WHERE 1";
    if ($postcode_cijfer) {
      $sql .= " AND ('".$postcode_cijfer."' BETWEEN `".$van_postcode_num."` AND `".$tot_postcode_num."`)";
    }
    if ($postcode_letter) {
      $sql .= " AND ('".strtoupper($postcode_letter)."' BETWEEN UPPER(`".$van_postcode_letter."`) AND UPPER(`".$tot_postcode_letter."`))";
    }
    
    $dao = CRM_Core_DAO::executeQuery($sql);
    $return = array();
    while($dao->fetch()) {
      $return[] = $dao->entity_id;
    }
    
    return array_unique($return);
  }
  
  /**
   * Returns the table name of a custom group
   * 
   * @param id $group_id
   * @return array
   */
  private function getCustomGroupTableById($group_id) {
    $params['id'] = $group_id;
    $result = civicrm_api3('CustomGroup', 'getsingle', $params);
    return $result['table_name'];
  }
  
  /**
   * Returns the column name of a custom field retrieved by its name and group_id
   * 
   * @param String $name
   * @param int $group_id
   * @return array
   */
  private function getCustomFieldColumnByNameAndGroup($name, $group_id) {
    $params['custom_group_id'] = $group_id;
    $params['name'] = $name;
    $result = civicrm_api3('CustomField', 'getsingle', $params);
    return $result['column_name'];
  }
  
  /**
   * Returns the id of a custom group, only contact groups are checked
   * 
   * @param string $name
   * @return int
   */
  private function getCustomGroupIdByName($name) {
    $params['name'] = $name;
    $params['extends'] = 'Contact';
    $result = civicrm_api3('CustomGroup', 'getsingle', $params);
    return $result['id'];
  }
  
}
