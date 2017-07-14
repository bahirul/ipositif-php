<?php
namespace app\base;

use Respect\Validation\Validator as v;

class DomainValidator{

	/**
	 * validate domain
	 *
	 * @param string $domain [description]
	 *
	 * @return boolean
	 */
	public function validate($domain){
		$tld = false;

		//replace -. caracter with .
		$domain = str_replace('-.','.', $domain);

		return v::domain($tld)->validate($domain);
	}

}