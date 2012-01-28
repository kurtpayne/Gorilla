<?php
/**
 * Case-insensitive dictionary, suitable for HTTP headers
 *
 * @package Requests
 */

/**
 * Case-insensitive dictionary, suitable for HTTP headers
 *
 * @package Requests
 */
class Requests_Response_Headers implements ArrayAccess, IteratorAggregate {
	protected $data = array();

	public function offsetExists($key) {
		$key = strtolower($key);
		return isset($this->data[$key]);
	}

	public function offsetGet($key) {
		$key = strtolower($key);
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}

	public function offsetSet($key, $value) {
		if (is_null($key)) {
			throw new Requests_Exception('Headers is a dictionary, not a list', 'invalidset');
		}

		$this->data[strtolower($key)] = $value;
	}

	public function offsetUnset($key) {
		unset($this->data[strtolower($key)]);
	}

	public function getIterator() {
		return new ArrayIterator($this->data);
	}
}