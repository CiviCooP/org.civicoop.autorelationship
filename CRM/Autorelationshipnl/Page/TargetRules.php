<?php

require_once 'CRM/Core/Page.php';

class CRM_Autorelationshipnl_Page_TargetRules extends CRM_Core_Page {

  protected $_contactId;
  
  /**
   * The action
   * 
   * @var String 
   */
  protected $_action = 'list';

  /**
   * The selected ID for the action
   * 
   * @var int 
   */
  protected $_entity_id = '';

  /**
   * The entity for the action
   * @var type 
   */
  protected $_entity_type = '';

  public function run() {
    $this->retrieveRequestData();
    $this->assign('contactId', $this->_contactId);

    switch ($this->_action) {
      case 'list':
        $this->listAction();
        break;
      case 'delete':
        $this->deleteAction();
        break;
    }

    parent::run();
  }

  /**
   * List action
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 21 Feb 2014
   * @access protected
   */
  protected function listAction() {
    $factory = CRM_Autorelationshipnl_TargetFactory::singleton();

    $entities = $factory->getEntityList($this->_contactId);
    $this->assign('entities', $entities);
  }

  /**
   * Delete action
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 21 Feb 2014
   * @access protected
   */
  protected function deleteAction() {
    $factory = CRM_Autorelationshipnl_TargetFactory::singleton();
    $factory->deleteEntity($this->_entity_type, $this->_entity_id, $this->_contactId);
    
    $session = CRM_Core_Session::singleton();
    $session->setStatus(ts("Automatic relationship rule removed."), ts("Delete"), 'success');
    
    $this->redirectToTab();
  }
  
  protected function redirectToTab() {
    $url = 'civicrm/contact/view';
    $q = 'action=browse&reset=1&selectedChild=autorelationship_targetrules&cid='.$this->_contactId;
    
    $redirectUrl = CRM_Utils_System::url($url, $q, TRUE);
    CRM_Utils_System::redirect($redirectUrl);
  }

  /**
   * Function to retrieve data from REQUEST
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 21 Feb 2014
   * @access protected
   */
  protected function retrieveRequestData() {
    $this->_contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);

    $action = CRM_Utils_Request::retrieve('action', 'String');

    if (isset($action) && $action == CRM_Core_Action::DELETE) {
      $this->_action = 'delete';
      $this->_entity_type = CRM_Utils_Request::retrieve('entity', 'String', $this, TRUE);
      $this->_entity_id = CRM_Utils_Request::retrieve('entity_id', 'Positive', $this, TRUE);
    }
  }

}
