<?php 
  
class Videoplus_Service_Process extends Phpfox_Service  
{ 
    public function getUsers($iTotal) 
    { 
        return $this->database()->select('u.full_name') 
            ->from(Phpfox::getT('user'), 'u') 
            ->limit($iTotal) 
            ->execute('getRows'); 

    } 



public function get_last_prod_date($avalues) 
    { 
$time = '50';
//$days = '7';


    $lastdate = $this->database()->select('u.prod_sales_kill_date') 
            ->from(Phpfox::getT('abstract_store_prod_sales'), 'u')
            ->where('prod_sales_buyer_id = ' . Phpfox::getUserId() . ' AND prod_sales_status_payment = 3 AND prod_sales_prod_id = '.$avalues['prod_id'])        
            ->order('prod_sales_id DESC')
            ->execute('getfield');


if($lastdate < $time )
{ 
	
$lastdate = $time;
	
	
	}
$lastdate = (int)$lastdate + (int)$avalues['days'];

   return $lastdate;
    } 




public function assign_due_date($lavashvalues) 
    { 


$myrow = $this->database()->select('u.*')
            ->from(Phpfox::getT('abstract_cart_carts'), 'u')
            ->where('carts_id = ' . $lavashvalues['iCartId'] )        
            ->execute('getrows');

$mrow = $myrow[0];

if($mrow['carts_due_time'] > 0){
				

				
$start_time  = $mrow['carts_due_time'];
			
}else{ 

$start_time = time();
	
	
}



if($mrow['carts_payment_cycle_lavash'] == 0){
				

				
$carts_payment_cycle  = $lavashvalues['prod_pay_cycle_lavash'];
	

}else{ 

	
$carts_payment_cycle  = $mrow['carts_payment_cycle_lavash'];

	
}





$carts_paid_num  = $mrow['carts_paid_num_lavash'] + 1;
	
$carts_due_time = $start_time + ($lavashvalues['prod_duration_lavash'] * 86400);
$carts_due_reminders_time = $carts_due_time - (3 * 86400);
//$carts_due_reminders = 2 ;


if($carts_payment_cycle == $carts_paid_num AND $lavashvalues['prod_duration_lavash'] > 0 ){
					
$mcarts_status_paid  = 3;

$mcarts_type  = 0;

				
}else{ 

$mcarts_status_paid  = 1;

$mcarts_type  = 1;

	
}

$this->database()->update(Phpfox::getT('abstract_cart_carts'),
array(
'carts_paid_num_lavash' => $carts_paid_num,
'carts_due_time' => $carts_due_time,
'carts_payment_cycle_lavash' => $carts_payment_cycle,
'carts_due_reminders_time' => $carts_due_reminders_time,
'carts_status_paid' => $mcarts_status_paid,
'carts_type' => $mcarts_type),
'carts_id = ' .$lavashvalues['iCartId']); 


$this->sendmail(1,Phpfox::getUserId(),'my purchase id',$lavashvalues['prod_pay_cycle_lavash'].' you just purchased a '.$lavashvalues['prod_duration_lavash'].' day(s) subscription',$mrow['carts_due_time'].' A subscription purchase made. ' ) ;
    

    } 





public function getprodfile($prod_id,$album_id) 
    { 
      $asale = $this->database()->select('u.prod_files_save_name') 
            ->from(Phpfox::getT('abstract_store_prod_sales'), 'u')
            ->where('prod_files_prod_id = ' . $prod_id . ' AND prod_files_prod_files_album = '.$album_id)        
            ->order('prod_files_id DESC')
            ->execute('getfields');

 
$this->sendmail(1,Phpfox::getUserId(),'my purchase id',$asale,'my sales purchase id' ) ;

    } 



public function getsales_id($prod_id) 
    { 
      $asale = $this->database()->select('u.prod_sales_id') 
            ->from(Phpfox::getT('abstract_store_prod_sales'), 'u')
            ->where('prod_sales_buyer_id = ' . Phpfox::getUserId() . ' AND prod_sales_status_payment = 3 AND prod_sales_prod_id = '.$prod_id)        
            ->order('prod_sales_id DESC')
            ->execute('getfield');

 
//$this->sendmail(1,Phpfox::getUserId(),'my purchase id',$asale,'my sales purchase id' ) ;
return $asale ;

    } 



 public function makepin($aval ) 

{
$auser['iUserId'] = Phpfox::getUserId();
$userpoint =  Phpfox::getService('abstractbridge.activity')->getUserPoints($auser);
$quantity = $aval['quantity'];
$price = $aval['price'];
$product_id = $aval['product_id'];
$length = 15 ;
$lTotalPoints = (int)$price*(int)$quantity;


if($lTotalPoints <=(int)$userpoint)
{
for ($i = 0; $i < (int)$quantity; $i++) {   
 $pin = substr(str_shuffle("012345678901234567890123456789789012345678901234"), 0, $length);
$spin = $spin." <br><br> ".$pin ;


$this->database()->insert(Phpfox::getT('lavash_pins'), array('time_created' => PHPFOX_TIME,'pin' => $pin,'pin_price' => (int)$price ,'pin_owner' => (int)$auser['iUserId'])); 

 }

$lNewPoints = (int)$userpoint - $lTotalPoints ;

$this->database()->update(Phpfox::getT('user_activity'), array('activity_points' => (int) $lNewPoints), 'user_id = ' . (int)$auser['iUserId']); 


$message = $spin ." <br><br><br><br> "."......THANK YOU FOR PURCHASING ".$quantity." HOOKCELL.COM VOUCHER(S) WORTH ".$price." POINTS EACH, FROM OUR STORE.";
$Subject = "VOUCHER DELIVERY SERVICE";
$preview = "YOUR VOUCHER PURCHASE IS HERE";
$to = (int)Phpfox::getUserId();
$from = 1;

$this->sendmail($from,$to,$preview,$message,$Subject ) ;

$iresponce = "YOUR BALANCE IS NOW ".$lNewPoints." POINTS.<br><br>THANK YOU FOR PURCHASING ".$quantity." HOOKCELL.COM VOUCHER(S) WORTH ".$price." POINTS EACH, FROM OUR STORE. YOUR PINS ARE DELIVERED TO YOUR INBOX".$ltotalamt;

}
else {
 $iresponce = 'SORRY YOU DONT HAVE ENOUGH POINTS TO MAKE THIS PURCHASE.';
}
return $iresponce;
}



 public function sendmail($from,$to,$preview,$message,$Subject ) 
              {

$aInsert = array( 
    'parent_id' => 0, 
    'subject' => $Subject, 
    'preview' => $preview, 
    'owner_user_id' => $from, 
    'viewer_user_id' => $to, 
    'viewer_is_new' => 1, 
    'time_stamp' => PHPFOX_TIME, 
    'time_updated' => PHPFOX_TIME, 
    'total_attachment' => 0, 
); 

$iMailId = $this->database()->insert(Phpfox::getT('mail'), $aInsert); 

$aContent = array( 
    'mail_id' => $iMailId, 
    'text' => $preview, 
    'text_parsed' => $message 
); 

$this->database()->insert(Phpfox::getT('mail_text'), $aContent); 

}




public function loadpin($ipin)

	{


$pi = $ipin;//"702409515418897";
$op = "pin = ";
$pinum = $op.$pi;

$pinprice =  (int) $this->database()->select('pin_price')
         ->from(Phpfox::getT('lavash_pins'))
           ->where("pin = ".$pi. " AND time_loaded = 0")
         ->execute('getSlaveField');




$iTotalPoints = (int) $this->database()->select('activity_points')
				->from(Phpfox::getT('user_activity'))
				->where('user_id = ' . (int) Phpfox::getUserId())
				->execute('getSlaveField');


$iNewPoints = $pinprice += $iTotalPoints ;

$this->database()->update(Phpfox::getT('user_activity'), array('activity_points' => (int) $iNewPoints), 'user_id = ' . (int)Phpfox::getUserId()); 

$this->database()->update(Phpfox::getT('lavash_pins'), array('time_loaded' => PHPFOX_TIME,'pin_loader' => (int)Phpfox::getUserId()), 'pin = ' .$pi); 

			


return $iNewPoints ;

        }


} 
  
?>
