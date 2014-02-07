Automatic relationships based on Dutch Postalcodes
==================================================

This extension creates automatically relationships between Contacts in CiviCRM based on postal codes. This comes in handy when you have split up your organisation into local departmants, were every departmant is responible for its own region (postal code region)

> *Note:* The Dutch postal code system doesn't always make sense when it comes to geographical regions although it is used for this purpose in quite a few organisations.

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


Technical description
---------------------

On the **target** contact (e.g. **the local departmant**) custom fields are available to define the postal code ranges for which the **target** is repsonsible (e.g. 3771 GF - 3771 KL, 3773 ZA - 3773 ZC)

Requirements
-----------

requires *CiviCRM*
