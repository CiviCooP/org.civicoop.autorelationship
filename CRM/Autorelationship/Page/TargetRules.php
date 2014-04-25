<?php

require_once 'CRM/Core/Page.php';

class CRM_Autorelationship_Page_TargetRules extends CRM_Core_Page {

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
      case 'add':
        $this->addAction();
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
    
    $this->setUserContext();
    
    $factory = CRM_Autorelationship_TargetFactory::singleton();

    $entities = $factory->getEntityList($this->_contactId);
    $this->assign('entities', $entities);
    $this->assign('interfaces', $factory->getTargetInterfacesForContact($this->_contactId));
  }

  /**
   * Delete action
   * 
   * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
   * @date 21 Feb 2014
   * @access protected
   */
  protected function deleteAction() {
    $factory = CRM_Autorelationship_TargetFactory::singleton();
    $factory->deleteEntity($this->_entity_type, $this->_entity_id, $this->_contactId);
    
    $session = CRM_Core_Session::singleton();
    $session->setStatus(ts("Automatic relationship rule removed."), ts("Delete"), 'success');
    
    $redirectUrl = $session->popUserContext();
    CRM_Utils_System::redirect($redirectUrl);
  }
  
  protected function addAction() {
    $factory = CRM_Autorelationship_TargetFactory::singleton();
    $interface = $factory->getInterfaceForEntity($this->_entity_type);
    $url = $interface->getAddFormUrl();
    $q = 'cid='.$this->_contactId;
    $q .= '&entity='.$this->_entity_type;
    
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
    } elseif (isset($action) && $action == CRM_Core_Action::ADD) {
      $this->_action = 'add';
      $this->_entity_type = CRM_Utils_Request::retrieve('entity', 'String', $this, TRUE);
    }
  }
  
  /**
   * Sets the context to the user tab TargetRules
   */
  protected function setUserContext() {
    $session = CRM_Core_Session::singleton();
    $userContext = CRM_Utils_System::url('civicrm/contact/view', 'cid='.$this->_contactId.'&selectedChild=autorelationship_targetrules&reset=1');
    $session->pushUserContext($userContext);
  }

}
