<?php
namespace app\controllers;

use app\base\BaseController;

class KominfoController extends BaseController {

	/**
	 * kominfo blacklist download
	 *
	 * @return string binary file output
	 */
	public function blacklist(){
		
		$file = __DIR__.'/../data/kominfo/blacklist/blacklist.domain';

		if(file_exists($file)){
			//return binary file download
			return $this->file($file,'kominfo.blacklist.domain');
		}

		//return empty content
		return '';
	}

	/**
	 * kominfo whitelist download
	 *
	 * @return string binary file output
	 */
	public function whitelist(){
		
		$file = __DIR__.'/../data/kominfo/whitelist/whitelist.domain';

		if(file_exists($file)){
			//return binary file download
			return $this->file($file,'kominfo.whitelist.domain');
		}

		//return empty content
		return '';
	}

}