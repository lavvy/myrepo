<?php



class Videoplus_Service_apimethods_loadcredit extends Phpfox_Service 

{

public function __construct()

	{
                

		$this->_oApi = Phpfox::getService('api');
                
		
	}

public function process()

	{
$this->_sTable = Phpfox::getT('user_activity');

$pin = $this->_oApi->get('pin'); 

$aRows = (int) $this->database()->select('points')
         ->from('lavash_pins')
           ->where('pins = '.$pin)
         ->execute('getSlaveField');


$iTotalPoints = (int) $this->database()->select('activity_points')
				->from(Phpfox::getT('user_activity'))
				->where('user_id = ' . (int) Phpfox::getUserId())
				->execute('getSlaveField');


$iNewPoints = $aRows += $iTotalPoints ;

$this->database()->update(Phpfox::getT('user_activity'), array('activity_points' => (int) $iNewPoints), 'user_id = ' . (int)Phpfox::getUserId()); 

$this->database()->update('lavash_pins', array('time_loaded' => PHPFOX_TIME,'pin_loader' => (int)Phpfox::getUserId()), $pinum); 
				
return $iNewPoints ;

        }
}

?>
