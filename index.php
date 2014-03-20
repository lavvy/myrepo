<?php
/**
 * [PHP_HEADER]
 */

defined('PHP') or exit('NO DICE!');

/**


class Videoplus_Service_apimethods_getmymails extends Php_Service 

  {




  public function process()
	{
	return	Php::getService('mail.api')->get();
	}
}

?>
