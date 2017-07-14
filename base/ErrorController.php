<?php
namespace app\base;

class ErrorController extends BaseController{

	/**
	 * default method for error controller
	 *
	 * @param integer $statusCode    error code
	 * @param string  $statusMessage error message
	 *
	 * @return string render view
	 */
	public function index($statusCode=500,$statusMessage='An internal server error has occurred'){
		return $this->render('app/error.twig',['statusCode' => $statusCode,'statusMessage' => $statusMessage]);
	}

}