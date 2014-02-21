Automatic relationships based on Dutch Postalcodes
==================================================

This extension creates automatically relationships between Contacts in CiviCRM based on postal codes. This comes in handy when you have split up your organisation into local departmants, were every departmant is responible for its own region (postal code region)

> **Note:** The Dutch postal code system doesn't always make sense when it comes to geographical regions although it is used for this purpose in quite a few organisations.

Installation instructions
-------------------------

1. Download and extract the extension into your CiviCRM extension directory
2. Install and enable the extension
3. Setup the custom field group **Responsible Postal Codes** for the right contact type (**by default it is applicable for all contacts**

Usage instructions
------------------

1. Setup a **target** contact to fill in the postal code ranges on the tab **Responsible Postal Codes**
2. Select an another contact and fill in a postal code and select country Netherlands. 
3. The contact and target have now a relationship with each other

> **Note:** only primary addresses are used for matching relationships.

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
