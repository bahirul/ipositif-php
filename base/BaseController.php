<?php
namespace app\base;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Twig_Environment;

class BaseController{

	private $request;
	private $response;
	private $twig;
	private $binResponse;

	/**
	 * __construct class constructor
	 *
	 * @param Request          $request  request
	 * @param Response         $response response
	 * @param Twig_Environment $twig     Twig_Environment
	 */
	public function __construct(Request $request,Response $response,Twig_Environment $twig){
		$this->request = $request;
		$this->response = $response;
		$this->twig = $twig;
	}

	/**
	 * controller render view
	 *
	 * @param string $view view file
	 * @param array  $data view data
	 *
	 * @return [type] [description]
	 */
	public function render($view,$data=[]){
		return new Response($this->twig->render($view,$data));
	}

	/**
	 * controller download file
	 *
	 * @param string $file file path
	 *
	 * @return string download output
	 */
	public function file($file,$attachment_name=''){
		$response = new BinaryFileResponse($file);

		$response->setContentDisposition(
    		ResponseHeaderBag::DISPOSITION_ATTACHMENT,
    		$attachment_name
		);

		return $response;
	}

}