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


class Videoplus_Service_apimethods_getfeaturedhome extends Phpfox_Service 

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


 if ($addon_type == "music")
{
$cat = 9;
  
 }
 

 if ($addon_type == "video")
{

$cat = 9;
  
 }
 if ($addon_type == "live")
{

$cat = 9;
  
 }
 if ($addon_type == "radio")
{

$cat = 9;
  
 }
 if ($addon_type == "event")
{

$cat = 9;
  
 }

$array = $this->database()->select('*') 
            ->from(Phpfox::getT('abstract_store_prod_sales'), 'u')
            ->leftJoin(Phpfox::getT('abstract_store_prod_files'),'ui', 'ui.prod_files_prod_id = u.prod_sales_prod_id')
            ->leftJoin(Phpfox::getT('abstract_store_prod_gallery'),'uh', 'uh.prod_gallery_prod_id = u.prod_sales_prod_id')
            ->leftJoin(Phpfox::getT('abstract_store_prod'),'um', 'um.prod_id = u.prod_sales_prod_id')
            ->where('u.prod_sales_buyer_id = ' . Phpfox::getUserId() . ' AND u.prod_sales_status_payment = 3 AND um.prod_approved = 1 AND um.prod_active = 0 AND um.prod_c1 = '.$cat)
            ->limit($ipage,6)    
            ->order('u.prod_sales_time DESC')
            ->execute('getrows');



foreach ($array as $result) { 

$answer['title'] = $result['prod_name'];
$answer['description'] = $result['prod_descr'];
$answer['photo'] = $result['prod_gallery_destination'];
$answer['link'] = $result['prod_files_save_name'];
$answer['id'] = 'video';

$result2[] = $answer;

  }  

$array1 = $this->database()->select('*') 
            ->from(Phpfox::getT('abstract_store_prod_sales'), 'u')
            ->leftJoin(Phpfox::getT('abstract_store_prod_files'),'ui', 'ui.prod_files_prod_id = u.prod_sales_prod_id')
            ->leftJoin(Phpfox::getT('abstract_store_prod_gallery'),'uh', 'uh.prod_gallery_prod_id = u.prod_sales_prod_id')
            ->leftJoin(Phpfox::getT('abstract_store_prod'),'um', 'um.prod_id = u.prod_sales_prod_id')
            ->where('u.prod_sales_buyer_id = ' . Phpfox::getUserId() . ' AND u.prod_sales_status_payment = 3 AND um.prod_approved = 1 AND um.prod_active = 0 AND um.prod_c1 = '.$cat)
            ->limit($ipage,6)    
            ->order('u.prod_sales_time DESC')
            ->execute('getrows');



foreach ($array1 as $result1) { 

$answer1['title'] = $result1['prod_name'];
$answer1['description'] = $result1['prod_descr'];
$answer1['photo'] = $result1['prod_gallery_destination'];
$answer1['link'] = $result1['prod_files_save_name'];
$answer1['id'] = 'music';

$result2[] = $answer1;

  }  

$array5 = $this->database()->select('*') 
            ->from(Phpfox::getT('abstract_store_prod_sales'), 'u')
            ->leftJoin(Phpfox::getT('abstract_store_prod_files'),'ui', 'ui.prod_files_prod_id = u.prod_sales_prod_id')
            ->leftJoin(Phpfox::getT('abstract_store_prod_gallery'),'uh', 'uh.prod_gallery_prod_id = u.prod_sales_prod_id')
            ->leftJoin(Phpfox::getT('abstract_store_prod'),'um', 'um.prod_id = u.prod_sales_prod_id')
            ->where('u.prod_sales_buyer_id = ' . Phpfox::getUserId() . ' AND u.prod_sales_status_payment = 3 AND um.prod_approved = 1 AND um.prod_active = 0 AND um.prod_c1 = '.$cat)
            ->limit($ipage,6)    
            ->order('u.prod_sales_time DESC')
            ->execute('getrows');



foreach ($array5 as $result5) { 

$answer5['title'] = $result5['prod_name'];
$answer5['description'] = $result5['prod_descr'];
$answer5['photo'] = $result5['prod_gallery_destination'];
$answer5['link'] = $result5['prod_files_save_name'];
$answer5['id'] = 'live';

$result2[] = $answer5;

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
