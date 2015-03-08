<?php 
class Videoplus_Service_apimethods_buyitem extends Phpfox_Service  
{






public function __construct()

	{
                

		$this->_oApi = Phpfox::getService('api');
                
		
	}


public function process($cartid)
	{
		
		/*
		*	EXTENDS ABSTRACT BRIDGE  	
		*/
		$oServAbsBrSite = Phpfox::getService('abstractbridge.site');
		$sLoadBridge = $oServAbsBrSite->runBridge();
		eval($sLoadBridge);
		/*
		*	CONFIG OBJECT 
		*/
		$aObject = array('sType'=>'service','sModule'=>'abstractcart','sClass'=>'config');
		$oCartConfig = $oServAbsBrSite->loadObject($aObject);
			unset($aObject);
		/*
		*	OBJECT LOADING  
		*/
		$aObjects = array('aObjCartCart'=>'oCartCart','aObjCartCallbacks'=>'oCartCallbacks','aObjCartCallback'=>'oCartCallback');
		$sLoadObjects = $oCartConfig->runCart($aObjects);
		eval($sLoadObjects);


                $aCon = $oCartConfig->getConfig();



// get an item
$itemid = $this->_oApi->get('itemid'); 

// create a new cart
$cartid = Phpfox::getService('abstractcart.cart')->insertCart();

// add or insert an item to the new created cart and lock the cart

			$aCheckoutOptionsAll['aCheckoutOptions'] = $aCheckoutInputs;
			$aCheckoutOptionsAll['iQuantity'] = 1;
			
			$aParams['iCartId'] = $cartid;
			$aParams['sModuleId'] = 'abstractstore';
			$aParams['iItemType'] = '0';
			$aParams['iItemId'] = $itemid;
			
			$aParams['sActionModuleId'] = '';
			$aParams['iActionItemType'] = '0';
			$aParams['iActionItemId'] = '0';
			
			
			
			$aParams['iUserId'] = 1;
			$aParams['sCheckoutOptions'] = 'a:2:{s:16:"aCheckoutOptions";a:0:{}s:9:"iQuantity";s:1:"1";}';
		        $iCartItemId = Phpfox::getService('abstractcart.cart')->insertCartItem($aParams);

 

 //lock the Cart  
$aUpdateCartParams['aVals']['carts_id'] = $cartid;
$aUpdateCartParams['aVals']['carts_status_paid'] = 1;
$ans = Phpfox::getService('abstractcart.cart')->updateCart($aUpdateCartParams); 



//review the cart

Phpfox::getService('videoplus.buy1')->process($cartid);

// pay for this cart with points



			
		// get cart
                $aGetCartParams['bSearchAllCarts'] = true;
		$aGetCartParams['iCartId'] = $cartid;
		$aGetCartParams['bCartItems'] = true;
		$aCart = $oCartCart->getCart($aGetCartParams); 	


			
			//Verify Points again here 
			//Determine Point Value of Cart Total 
			$iFinalTotal = number_format($aCart['iFinalTotal'], 2,'.',''); 
			$iFinalTotalPoints = intval($iFinalTotal * $aCon['points_exchange']); 
			//echo $iFinalTotalPoints;
			
			//Get User 
			$iUserId = $oServAbsBrUser->getUserId();
			
			//Get User Points 
			$aUserPointsParams['iUserId'] = $iUserId;
			$iUserPoints = $oServAbsBrActivity->getUserPoints($aUserPointsParams);
			
			//Does User have enough points? 
			if($iFinalTotalPoints <= $iUserPoints){ 
				
				
				
				
				//Remove Points from User Account 
				$aUpdateActivityParams['iUserId'] = $aCart['carts_user_id']; 
				$aUpdateActivityParams['bBypassItemActivity'] = true; 
				$aUpdateActivityParams['bBypassTotalActivity'] = true; 
				$aUpdateActivityParams['iAmount'] = $iFinalTotalPoints;
				$aUpdateActivityParams['sMethod'] = '-';
				$oServAbsBrActivity->update($aUpdateActivityParams); 
				
				//Update Cart's Point Variables Manually for reference later  
				$aUpdateCartParams['aVals']['carts_id'] = $aCart['carts_id'];
				$aUpdateCartParams['aVals']['carts_points'] = 1;  
				$aUpdateCartParams['aVals']['carts_points_amt'] = $iFinalTotalPoints;
                                $aUpdateCartParams['aVals']['carts_amt_total'] = $aCart['iFinalTotal'];  
				$aUpdateCartParams['aVals']['carts_pay_method_string'] = 'abspoints';
				$oCartCart->updateCart($aUpdateCartParams);
				
				//Run Cart processing. Set to completed. 
				$aPaymentCallbackParams['status'] = 'completed';
				$aPaymentCallbackParams['item_number'] = $aCart['carts_id'];
				$aPaymentCallbackParams['total_paid'] = $aCart['carts_amt_total'];
				$oCartCallback->paymentApiCallback($aPaymentCallbackParams); 
			
			
			} 
				
				
		}	
		
		
		

	
} 
?>
