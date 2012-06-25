<?php 

$plugin_info = array(
	'pi_name'			=> 'Interspire Email Marketer Subscriber',
	'pi_version'		=> '1.0',
	'pi_author'			=> 'Amity Web Solutions',
	'pi_author_url'		=> 'http://www.amitywebsolutions.co.uk',
	'pi_description'	=> 'Subscribe user data to your Interspire Email Marketer Contact List',
	'pi_usage'			=> Iem_subscriber::usage()
);

/**
 * Iem_subscribe class
 *
 * @package			ExpressionEngine
 * @category		Plugin
 * @author			Amity Web Solutions
 * @copyright		Copyright (c) 2012 Amity Web Solutions
 * @link			http://www.amitywebsolutions.co.uk
 */

class Iem_subscriber {
		
	function Iem_subscriber($str = '')
	{
		$this->EE =& get_instance();

		// Get the only required parameter
		$data['xml_path'] = $this->EE->TMPL->fetch_param('xml_path');
		$data['xml_username'] = $this->EE->TMPL->fetch_param('xml_username');
		$data['xml_usertoken'] = $this->EE->TMPL->fetch_param('xml_usertoken');
		$data['mailinglist'] = $this->EE->TMPL->fetch_param('mailinglist_id');
		$data['emailaddress'] = $this->EE->TMPL->fetch_param('emailaddress');

		// If there's no field data, then show an error
		if ( $data['xml_path'] == "" || $data['xml_username'] == "" || $data['xml_usertoken'] == "" || $data['mailinglist'] == "" || $data['emailaddress'] == "" )
		{
			$error_message .= '<p>Please ensure to complete all required fields.</p>';
			$this->return_data = $error_message;
		}
		else
		{
			// Get other data
			$data['format'] = $this->EE->TMPL->fetch_param('format');
			$data['format'] = ($data['format'] != '' ? $data['format'] : 'h');
			$data['confirmed'] = $this->EE->TMPL->fetch_param('confirmed');
			$data['confirmed'] = ($data['confirmed'] != '' ? $data['confirmed'] : 'y');
		
			//Get any custom data
			$custom_field_ids = $this->EE->TMPL->fetch_param('custom_field_ids');
			$custom_field_data = $this->EE->TMPL->fetch_param('custom_field_data');
			
			// Explode the custom fields into arrays
			if( $custom_field_ids != '' && $custom_field_data != '')
			{
				$custom_field_ids_row = explode('|', $custom_field_ids);
				$custom_field_data_row = explode('|', $custom_field_data);
			}
			
			// If the custom field counts are different, return error
			if( count($custom_field_ids_row) != count($custom_field_data_row))
			{
				$error_message .= '<p>The custom fields count does not match</p>';
				$this->return_data = $error_message;
			}
			// Otherwise merge the arrays into one array
			else
			{
				$data['custom_fields'] = array_combine($custom_field_ids_row, $custom_field_data_row);
			}
			
			// Get the XML data
			$xml = $this->getXml($data);

			// Send the XML data
			$xmlData = $this->sendXml($xml, $data['xml_path']);

		}

	}
	
	function getXML($data)
	{
		$xml = '
		<xmlrequest>
			<username>'.$data['xml_username'].'</username>
			<usertoken>'.$data['xml_usertoken'].'</usertoken>
			<requesttype>subscribers</requesttype>
			<requestmethod>AddSubscriberToList</requestmethod>
			<details>
				<emailaddress>'.$data['emailaddress'].'</emailaddress>
				<mailinglist>'.$data['mailinglist'].'</mailinglist>
				<format>'.$data['format'].'</format>
				<confirmed>'.$data['confirmed'].'</confirmed>';
		if( count($data['custom_fields'] > 0) )
		{
			$xml .= '<customfields>';
			foreach( $data['custom_fields'] AS $fieldid => $value)
			{
				$xml .= '
					<item>
						<fieldid>'.$fieldid.'</fieldid>
						<value>'.$value.'</value>
					</item>
					';
			}
			$xml .= '</customfields>';
		}
		$xml .= '</details>
		</xmlrequest>
		';
		return $xml;
	}
	
	function sendXML($xml, $xml_path, $return = 'result')
	{
		$ch = curl_init($xml_path);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$result = @curl_exec($ch);
		if($result === false)
		{
			$return = false;
		}
		else
		{
			$xml_doc = simplexml_load_string($result);
                    
			if ($xml_doc->status == 'SUCCESS')
			{
				if($xml_doc->data > 0)
				{
					if($return == 'data')
						return $xml_doc->data;
					else																
						$return = true;
				}
				else
				{
					$return = false;
				}
			}
			else
			{
					$return = false;
			}
		}
		return $return;
	}
		
	function usage()
	{
		ob_start(); 
		?>
		Use the following tag and parameters to pass data to your contact list:
		
		{exp:iem_subscriber 
			txml_path="http://YOUR_XML_PATH" 
		    xml_username="YOUR_XML_USERNAME" 
		    xml_usertoken="YOUR_XML_USERTOKEN" 
		    mailinglist_id="YOUR_MAILINGLIST_ID" 
		    format="h" 
		    confirmed="y" 
		    emailaddress="your@emailaddress_to_subscribe.com"
		    custom_field_ids="1|2|3"
		    custom_field_data="abc|def|ghi"
		}

		Format can be h (for html) or t (for text). Defaults to h if removed.
		Confirmed can be yes or no. Defaults to yes. 
		
	
		<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}
// END CLASS