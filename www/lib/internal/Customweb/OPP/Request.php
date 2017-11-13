<?php

/**
 *  * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2016 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 */


final class Customweb_OPP_Request {
	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_DELETE = 'DELETE';
	
	/**
	 *
	 * @var string
	 */
	private $url;
	
	/**
	 *
	 * @var string
	 */
	private $method = self::METHOD_POST;
	
	/**
	 *
	 * @var array
	 */
	private $data = null;

	/**
	 *
	 * @param string $url
	 */
	public function __construct($url){
		$this->url = $url;
	}

	/**
	 *
	 * @return string
	 */
	public function getUrl(){
		return $this->url;
	}

	/**
	 *
	 * @return string
	 */
	public function getMethod(){
		return $this->method;
	}

	/**
	 *
	 * @param string $method
	 * @return Customweb_OPP_Request
	 */
	public function setMethod($method){
		$this->method = strtoupper($method);
		return $this;
	}

	/**
	 *
	 * @return array
	 */
	public function getData(){
		return $this->data;
	}

	/**
	 *
	 * @param array $data
	 * @return Customweb_OPP_Request
	 */
	public function setData(array $data){
		$this->data = $data;
		return $this;
	}

	/**
	 *
	 * @return array
	 * @throws Exception
	 */
	public function send(){
		$url = new Customweb_Core_Url($this->url);
		
		$request = new Customweb_Core_Http_Request();
		if ($this->method == self::METHOD_GET || $this->method == self::METHOD_DELETE) {
			$url->appendQueryParameters($this->data);
		}
		elseif ($this->method == self::METHOD_POST) {
			$request->setContentType('application/x-www-form-urlencoded');
			$request->setBody($this->data);
		}
		$request->setMethod($this->method);
		$request->setUrl($url);
		$client = Customweb_Core_Http_Client_Factory::createClient();
		$response = new Customweb_Core_Http_Response($client->send($request));
		$headers = array_change_key_case($response->getParsedHeaders(), CASE_LOWER);

		if (isset($headers['content-type'])) {
			if (strpos(end($headers['content-type']), 'application/json') !== false) {
				return json_decode(trim($response->getBody()));
			}
		}
		return $response->getParsedBody();
	}
}