<?php

if (in_array('PHPUnit_TextUI_Command', get_declared_classes())) {
	require_once(dirname(dirname(__FILE__)) . '/classes/Gorilla.php');
	Gorilla::$runner = new Gorilla_Runner_Server($argv);
}

/**
 * Service document tests
 *
 * @package Gorilla
 * @subpackage API Tests
 */
class ServiceDocumentTest extends PHPUnit_Framework_TestCase {
	/**
	 * Constructor
	 *
	 * Set up the 
	 */
	public function __construct($name = NULL, array $data = array(), $dataName = '') {
		$this->uri = Gorilla::$runner->get_option('uri');
		$this->auth = Gorilla::$runner->get_option('auth');
		parent::__construct($name, $data, $dataName);
	}

	/**
	 * Get the service document
	 */
	public function serviceDocumentProvider() {
		$options = array(
			'useragent' => 'Gorilla/0.1 php-requests/' . Requests::VERSION
		);

		if (!empty($this->auth)) {
			$options = array(
				'auth' => array($this->auth['user'], $this->auth['pass'])
			);
		}

		$document = Requests::get($this->uri . '/service', array(), array(), $options);
		return array(array($document));
	}

	/**
	 * Test that we successfully retrieved a service document
	 *
	 * @dataProvider serviceDocumentProvider
	 */
	public function testServiceDocumentExists($document) {
		$status = sprintf('Site returned %d with body: %s', $document->status_code, $document->body);
		$this->assertEquals(200, $document->status_code, $status);
		Gorilla::$runner->report(Gorilla_Runner::REPORT_INFO, 'Service document found');
	}

	/**
	 * Test that the we have collections
	 *
	 * @dataProvider serviceDocumentProvider
	 * @depends testServiceDocumentExists
	 */
	public function testCollectionsExist($document) {
		$reader = new SimpleXMLElement($document->body);
		$reader->registerXPathNamespace('app', 'http://www.w3.org/2007/app');
		$found_collections = $reader->xpath('//app:collection');
		$collections = array();
		foreach ($found_collections as $col) {
			$title = $col->children('http://www.w3.org/2005/Atom');
			$title = $title->title;
			$accepted = array();
			// We need this because otherwise SimpleXML gets funky with >1 'accept'
			foreach($col->accept as $accept) {
				$accepted[] = (string) $accept;
			}
			$accept = implode(', ', (array) $accepted);
			$collections[] = $title . ' accepts ' . $accept;
		}
		Gorilla::$runner->reportList(Gorilla_Runner::REPORT_INFO, 'Collections found:', $collections);
		$this->assertNotEmpty($collections);
	}
}