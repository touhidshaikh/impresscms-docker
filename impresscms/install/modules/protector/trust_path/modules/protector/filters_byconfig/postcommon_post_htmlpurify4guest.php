<?php

class protector_postcommon_post_htmlpurify4guest extends ProtectorFilterAbstract {
	var $purifier;
	var $method;

	function execute() {

		if (is_object(icms::$user)) {
			return true;
		}

		// use HTMLPurifier inside ImpressCMS
		if (class_exists('icms_core_HTMLFilter')) {
			$this->purifier = &icms_core_HTMLFilter::getInstance();
			$this->method = 'htmlpurify';
		} else {
			// use HTMLPurifier inside Protector
			require_once dirname(__DIR__) . '/library/HTMLPurifier.auto.php';
			$config = HTMLPurifier_Config::createDefault();
			$config->set('Cache', 'SerializerPath', ICMS_TRUST_PATH . '/modules/protector/configs');
			$config->set('Core', 'Encoding', _CHARSET);
			// $config->set('HTML', 'Doctype', 'HTML 4.01 Transitional');
			$this->purifier = new HTMLPurifier($config);
			$this->method = 'purify';
		}

		$_POST = $this->purify_recursive($_POST);
	}

	function purify_recursive($data) {
		if (is_array($data)) {
			return array_map(array (
				$this,
				'purify_recursive'
			), $data);
		} else {
			return strlen($data) > 32 ? call_user_func(array (
				$this->purifier,
				$this->method
			), $data) : $data;
		}
	}
}
