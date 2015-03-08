<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 * 
 * 
 * @copyright		[HOOKCELL_COPYRIGHT]
 * @author  		uche_okonkwo
 * @package 		Phpfox_Service
 * @version 		$Id: 0.1 
 */


class Videoplus_Service_apimethods_getpurchasedproduct extends Phpfox_Service 

{


public function __construct()

	{
                

		$this->_oApi = Phpfox::getService('api');
                
		
	}



public function process()

	{
$this->_sTable = Phpfox::getT('user_activity');


$ipage = $this->_oApi->get('ipage');
$addon_type = $this->_oApi->get('addon_type'); 
$addon_todo = $this->_oApi->get('addon_todo'); 


if ($addon_todo == "emymusic") {
    $cat = 15;
} 
elseif ($addon_type == "video") {
    $cat = 9;
}
elseif ($addon_type == "live") {
    $cat = 9;
}
elseif ($addon_todo == "shopvideo") 
{
    $cat = 9;
}
elseif ($addon_todo == "shopmusic") 
{
    $cat = 9;
}
elseif ($addon_todo == "shoplive") 
{
    $cat = 9;
}

elseif ($addon_todo == "event") {
    $cat = 9;
}
elseif ($addon_todo == "tv") {
    $cat = 9;
} 
elseif ($addon_todo == "radio") {
    $cat = 9;
}
elseif ($addon_todo == "featuredtv") {
    $cat = 9;
}
elseif ($addon_todo == "featuredradio") {
    $cat = 9;
}
elseif ($addon_todo == "myvideo") {
    $cat = 9;
}
elseif ($addon_todo == "mytvshow") {
    $cat = 9;
}
elseif ($addon_todo == "featuredvideo") {
    $cat = 9;
}
elseif ($addon_todo == "featuredtvshow") {
    $cat = 9;
}
elseif ($addon_todo == "mymusic") {
    $cat = 15;
}
elseif ($addon_todo == "myalbum") {
    $cat = 15;
}
elseif ($addon_todo == "featuredmusic") {
    $cat = 9;
}
elseif ($addon_todo == "featuredalbum") {
    $cat = 9;
}
else {
    $cat = 3;
}




$array = $this->database()->select('*') 
            ->from(Phpfox::getT('abstract_store_prod_sales'), 'u')
            ->leftJoin(Phpfox::getT('abstract_store_prod_files'),'ui', 'ui.prod_files_prod_id = u.prod_sales_prod_id')
            ->leftJoin(Phpfox::getT('abstract_store_prod_gallery'),'uh', 'uh.prod_gallery_prod_id = u.prod_sales_prod_id')
            ->leftJoin(Phpfox::getT('abstract_store_prod'),'um', 'um.prod_id = u.prod_sales_prod_id')
            ->where('u.prod_sales_buyer_id = ' . Phpfox::getUserId() . ' AND u.prod_sales_status_payment = 3 AND um.prod_approved = 

1 AND um.prod_active = 0 AND um.prod_c1 = '.$cat)
            ->limit($ipage,10)    
            ->order('u.prod_sales_time DESC')
            ->execute('getrows');



foreach ($array as $result) { 





$answer['title'] = $result['prod_name'];
$answer['description'] = $result['prod_descr'];
$answer['photo'] = $result['prod_gallery_destination'];
$answer['link'] = $result['prod_files_save_name'];
$answer['id'] = $result['prod_id'];

$result2[] = $answer;

  }  
return $result2;

        }






//////////////////////////////////////////////////////////////////////////

public function process2()
	{
//$result = array(); 
$array = $this->database()->select('u.prod_sales_prod_id') 
            ->from(Phpfox::getT('abstract_store_prod_sales'), 'u')
            ->where('prod_sales_buyer_id = ' . Phpfox::getUserId() . ' AND prod_sales_status_payment = 3 ')   
            ->limit(0,3)    
            ->order('prod_sales_id DESC')
            ->execute('getrows');


foreach ($array as $value) { 


   $val = $this->get($value['prod_sales_prod_id']);
  if ($val['title'] <>"")
{
$result[] = $val;
  
 }
  


  }  

return $result;

        }


public function get($prod_id)
	{
//$answer = array(); 
$ba =  $this->database()->select('*') 
            ->from(Phpfox::getT('abstract_store_prod'), 'u')
            ->leftJoin(Phpfox::getT('abstract_store_prod_files'),'ui', 'ui.prod_files_prod_id = '.$prod_id)
            ->leftJoin(Phpfox::getT('abstract_store_prod_gallery'),'uh', 'uh.prod_gallery_prod_id = '.$prod_id)
            ->where('u.prod_id = ' . $prod_id . ' AND u.prod_approved = 1 AND u.prod_active = 0 AND u.prod_c1 = 9')                
            ->order('u.prod_id DESC')
            ->execute('getrows');

$result = $ba[0];

$answer['title'] = $result['prod_name'];
$answer['description'] = $result['prod_descr'];
$answer['photo'] = $result['prod_gallery_destination'];
$answer['link'] = $result['prod_files_save_name'];
$answer['id'] = $result['prod_id'];

return $answer;
}






}

?>
