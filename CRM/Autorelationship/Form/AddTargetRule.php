<?php

/* 
 * Base class for adding a target rule
 */

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
abstract class CRM_Autorelationship_Form_AddTargetRule extends CRM_Core_Form {
  
  protected $targetContactId;
  
  protected $targetContact;
  
  protected $targetInterface;
  
  protected $entity;
  
  protected $new_entity_id;
  
  function preProcess() {
    $factory = CRM_Autorelationship_TargetFactory::singleton();
    $this->targetContactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $this->entity = CRM_Utils_Request::retrieve('entity', 'String', $this, TRUE);
    $this->targetInterface = $factory->getInterfaceForEntity($this->entity);
    
    $this->add('hidden', 'cid', $this->targetContactId);
    $this->add('hidden', 'entity', $this->entity);
    
    $this->targetContact = civicrm_api3('Contact', 'getsingle', array('id' => $this->targetContactId));
    $this->assign('contact', $this->targetContact);
    
    $this->setTitle(ts('Add automatic relation for ').$this->targetContact['display_name']);
       
    parent::preProcess();
  }
  
  public function buildQuickForm() {
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }
  
  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  protected function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
  
  public function postProcess() {
    parent::postProcess();
    
    if ($this->new_entity_id) {
      $this->onAddTargetRule($this->new_entity_id);
    }
  }
  
  /**
   * Function to be executed after a new target rule has be added to the system
   * 
   * @param int $entityId
   */
  protected function onAddTargetRule($entityId) {
    $matcher = $this->targetInterface->getMatcher();
    $matcher->matchAndCreateForTargetContactAndEntityId($this->targetContactId, $entityId);
  }
  
}
