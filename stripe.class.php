<?php

class Phpfox_Gateway_Api_Stripe implements Phpfox_Gateway_Interface
{
	/**
	 * Holds an ARRAY of settings to pass to the form
	 *
	 * @var array
	 */
	private $_aParam = array();
	
	/**
	 * Holds an ARRAY of supported currencies for this payment gateway
	 *
	 * @var array
	 */
	private $_aCurrency = array('USD','CAD','EUR','GBP','AUD');
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		
	}	
	
	/**
	 * Set the settings to be used with this class and prepare them so they are in an array
	 *
	 * @param array $aSetting ARRAY of settings to prepare
	 */
	public function set($aSetting)
	{
		$this->_aParam = $aSetting;
		
		if (Phpfox::getLib('parse.format')->isSerialized($aSetting['setting']))
		{
			$this->_aParam['setting'] = unserialize($aSetting['setting']);
		}
	}
	
	/**
	 * Each gateway has a unique list of params that must be passed with the HTML form when posting it
	 * to their site. This method creates that set of custom fields.
	 *
	 * @return array ARRAY of all the custom params
	 */
	public function getEditForm()
	{		
		return array(
			'test_secret_key' => array(
				'phrase' => Phpfox::getPhrase('stripe.test_secret_key'),
				'phrase_info' => Phpfox::getPhrase('stripe.this_is_the_test_secret_key_for_stripe_api'),
				'value' => (isset($this->_aParam['setting']['test_secret_key']) ? $this->_aParam['setting']['test_secret_key'] : '')
				),
			'test_publish_key' => array(
				'phrase' => Phpfox::getPhrase('stripe.test_publishable_key'),
				'phrase_info' => Phpfox::getPhrase('stripe.this_is_the_test_publishable_key_for_stripe_api'),
				'value' => (isset($this->_aParam['setting']['test_publish_key']) ? $this->_aParam['setting']['test_publish_key'] : '')
				),
			'live_secret_key' => array(
				'phrase' => Phpfox::getPhrase('stripe.live_secret_key'),
				'phrase_info' => Phpfox::getPhrase('stripe.this_is_the_live_secret_key_for_stripe_api'),
				'value' => (isset($this->_aParam['setting']['live_secret_key']) ? $this->_aParam['setting']['live_secret_key'] : '')
				),
			'live_publish_key' => array(
				'phrase' => Phpfox::getPhrase('stripe.live_publishable_key'),
				'phrase_info' => Phpfox::getPhrase('stripe.this_is_the_live_publishable_key_for_stripe_api'),
				'value' => (isset($this->_aParam['setting']['live_publish_key']) ? $this->_aParam['setting']['live_publish_key'] : '')
				)
		);
	}
	
	/**
	 * Returns the actual HTML <form> used to post information to the 3rd party gateway when purchasing
	 * an item using this specific payment gateway
	 *
	 * @return bool FALSE if we can't use this payment gateway to purchase this item or ARRAY if we have successfully created a form
	 */
	public function getForm()
	{		
		
		if (!in_array($this->_aParam['currency_code'], $this->_aCurrency))
		{
			if (isset($this->_aParam['alternative_cost']))
			{
				$aCosts = unserialize($this->_aParam['alternative_cost']);
				$bPassed = false;
				foreach ($aCosts as $sCode => $iPrice)
				{
					if (in_array($sCode, $this->_aCurrency))
					{
						$this->_aParam['amount'] = $iPrice;
						$this->_aParam['currency_code'] = $sCode;	
						$bPassed = true;
						break;
					}
				}
				
				if ($bPassed === false)
				{
					return false;
				}
			}
			else 
			{
				return false;
			}
		}
		
		if ($this->_aParam['is_test'])
		{
			$s_key =  $this->_aParam['setting']['test_secret_key'];
			$p_key =  $this->_aParam['setting']['test_publish_key'];
		}
		else
		{
			$s_key =  $this->_aParam['setting']['live_secret_key'];
			$p_key =  $this->_aParam['setting']['live_publish_key'];
		}
		
		
		
		
		if ($this->_aParam['recurring'] > 0)
		{
			$recurring_cost = '';
			if(empty($this->_aParam['recurring_cost']))
			{
				$recurringcost = unserialize($this->_aParam['alternative_recurring_cost']);
				
				foreach($recurringcost as $ikey=>$recur)
				{
					if($ikey == $this->_aParam['currency_code'])
					{
						$recurring_cost = $recur;
					}
				}
			}
			else
			{
				$recurring_cost = $this->_aParam['recurring_cost'];
			}
			
	      	$aForm = array(
			'url' => Phpfox::getLib('url')->makeUrl('stripe'),
			'param' => array(
				'secret_key' => $s_key,
				'publish_key' => $p_key,
				'total' => $this->_aParam['amount'],
				'currency_code' => $this->_aParam['currency_code'],
				'order_id' => $this->_aParam['item_number'],
				'item_number' => $this->_aParam['item_number'],
				'return_url' => Phpfox::getLib('gateway')->url('stripe'),
				'return'     => $this->_aParam['return'],
				'c_prod' => $this->_aParam['item_number'],
				'id_type' => '2',
				'c_name' => $this->_aParam['item_name'],
				'c_price' => $this->_aParam['amount'],
				'recurring' => $this->_aParam['recurring'],
				'recurring_cost' => $recurring_cost,
				'recurring_text' => $this->_aParam['recurring_cost']
				)
			);		
		}
		else
		{
			$aForm = array(
			'url' => Phpfox::getLib('url')->makeUrl('stripe'),
			'param' => array(
				'secret_key' => $s_key,
				'publish_key' => $p_key,
				'total' => $this->_aParam['amount'],
				'currency_code' => $this->_aParam['currency_code'],
				'order_id' => $this->_aParam['item_number'],
				'item_number' => $this->_aParam['item_number'],
				'return_url' => Phpfox::getLib('gateway')->url('stripe'),
				'return'     => $this->_aParam['return'],
				'c_prod' => $this->_aParam['item_number'],
				'id_type' => '2',
				'c_name' => $this->_aParam['item_name'],
				'c_price' => $this->_aParam['amount'],
				)
			);	
		}
		
		
		return $aForm;
	}
	
	/**
	 * Performs the callback routine when the 3rd party payment gateway sends back a request to the server,
	 * which we must then back and verify that it is a valid request. This then connects to a specific module
	 * based on the information passed when posting the form to the server.
	 *
	 */
	public function callback()
	{
				
	}
}

?>
