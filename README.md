## Description

An Expression Engine Plugin. The Interspire Email Marketer Subscriber plugin allows you to easily add user data, including custom fields, to an IEM contact list, from an Expression Engine template.

Possible uses are on a thank you page after submitting a form, such as Newsletter Subscribe form, or any other form. 

## Installation

Unzip and rename the folder to iem_subscriber. Upload the iem_subscriber folder to your system/expressionengine/third_party/ folder.

Go to Add-Ons Extensions and click Enable next to the extension Control Panel Shortcuts

## Usage

Use the following tag and parameters to pass data to your contact list:

	{exp:iem_subscriber 
		xml_path="http://YOUR_XML_PATH" 
	    xml_username="YOUR_XML_USERNAME" 
	    xml_usertoken="YOUR_XML_USERTOKEN" 
	    mailinglist_id="YOUR_MAILINGLIST_ID" 
	    format="h" 
	    confirmed="y" 
	    emailaddress="your@emailaddress_to_subscribe.com"
	    custom_field_ids="1|2|3"
	    custom_field_data="abc|def|ghi"
	}

- Format can be h (for html) or t (for text). Defaults to h if removed.
- Confirmed can be yes or no. Defaults to yes.
- Custom field ID and Data arays need to be in the same order, and each field separated with a pipe |.


## Bugs & Feature Requests

[Issue tracker](https://github.com/amityweb/iem_subscriber/issues)

Before reporting bugs or requesting any features, please check that it does not already exist.

## Current version

1.0