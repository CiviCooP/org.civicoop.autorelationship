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
  
  function preProcess() {
    $factory = CRM_Autorelationship_TargetFactory::singleton();
    $this->targetContactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);
    $entity = CRM_Utils_Request::retrieve('entity', 'String', $this, TRUE);
    $this->targetInterface = $factory->getInterfaceForEntity($entity);
    
    $this->add('hidden', 'cid', $this->targetContactId);
    $this->add('hidden', 'entity', $entity);
    
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
  
}
