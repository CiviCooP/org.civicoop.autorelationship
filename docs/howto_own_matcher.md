# How to build your own matcher

To create your own matcher you have to follow the steps below

1. Create a class which **implements**  `CRM_Autorelationship_TargetInterface`
2. Create a class which **extends** `CRM_Autorelationship_Matcher`
3. Implement hook `hook_autorelationship_targetinterfaces` 
4. Implement on your *'source entity*, e.g. Address the post hook so that the targets will be found on a post
5. Build the form to add rules on a target

### 1. Create a TargetInterface

See the file [CRM_Autorelationship_TargetInterface] (../CRM/Autorelationship/TargetInterface.php) for more information

### 2. Create a Matcher

See the file [CRM_Autorelationship_Matcher] (../CRM/Autorelationship/Matcher.php) for more information

Your class should extends the class above and implement the abstract methods.

### 3. Implement hook_autorelationship_targetinterfaces

For example:
    
    function hook_autorelationship_targetinterfaces(&$interfaces) {
        $interfaces[] = new CRM_Geostelsel_GemeenteTarget();
    }

### 4. Implement the post hook on your entity

For example when we want to match on an address field we should implement the post hook for the address field.

    function hook_civicrm_post( $op, $objectName, $objectId, &$objectRef ) {
        if ($objectName == 'Address' && $objectRef instanceof CRM_Core_DAO_Address) {
            $factory = CRM_Autorelationship_TargetFactory::singleton();
            $matcher = $factory->getMatcherForEntity('gemeente', array('address' => $objectRef));
            $matcher->matchAndCreateForSourceContact();
        }
    }

### 5. Build the form to add rules on a target

You can add a form to your matcher. As you don't know how to create a form, take a look at the [civicrm wiki] (http://wiki.civicrm.org/confluence/display/CRMDOC/Create+a+Module+Extension#CreateaModuleExtension-Addabasicwebform)

Add the link to this form to your target interface class.

Your target becomes available in the drop down on the tab for automatic relationships and the user will be redirected to the form above when they select your target.