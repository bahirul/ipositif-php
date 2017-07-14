<?php
namespace app\controllers;

use app\base\BaseController;

class DefaultController extends BaseController {

	/**
	 * default home action
	 *
	 * @return string render index
	 */
	public function index(){
		return $this->render('app/index.twig');
	}

}