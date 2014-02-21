Automatic relationships
==================================================

This extension creates automatically relationships between Contacts in CiviCRM based on rules of the target contact.
A rule is something as match on a particular city. It is possible to develop your own target rules.

This extension is developed for an organisation who wants their members be part of a local department. They wanted to achieve this automaticly.

Installation instructions
-------------------------

1. Download and extract the extension into your CiviCRM extension directory
2. Install and enable the extension
3. Select a target contact (e.g. a local department) and set up rules for creating an automatic relationship

Technical description
---------------------

The matching is done through a **Matcher** class. This extension has a abstract class for this matcher and has implemented an example of the matcher. Purpose of the matcher is to return target contact ids. In this case the matcher will match based on the postal code of the address and based on the range of postal codes of the target. 

### Requirements

requires *CiviCRM*

### Hooks

If you want to build your own matcher/target rules you have to implement the following hook

- **hook_autorelationship_targetinterfaces** this hook has one parameter which is the list of *TargetInterfaces*. You should return in the array the instance of your target interface which extends from *CRM_Autorelationshipnl_TargetInterface*

Future wishes & Todo
--------------------

For the future it would be handy if this extension becomes more flexibel, in that way that you can configure relationship type with an automatic flag, so one can have more automatic relationships

Also see the [ToDo](TODO.md)
