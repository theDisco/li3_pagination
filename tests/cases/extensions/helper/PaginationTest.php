<?php

namespace li3_pagination\tests\cases\extensions\helper;

use li3_pagination\extensions\helper\Pagination;
use lithium\action\Request;
use lithium\net\http\Router;
use lithium\tests\mocks\template\MockRenderer;

class PaginationTest extends \lithium\test\Unit {

	public $pagination;

	public $context;

	public function setUp() {

		$this->_routes = Router::get();
		Router::reset();
		Router::connect('/{:controller}/{:action}/page/{:page:[0-9]+}');

		$request = new Request();
		$request->params = array('controller' => 'posts', 'action' => 'index');
		$request->persist = array('controller');

		$this->context = new MockRenderer(compact('request'));
		$this->pagination = new Pagination(array('context' => $this->context));
	}

	public function tearDown() {}

	public function testCreatePagination() {
		$result = $this->pagination->create(100);
		$this->assertTags($result, array('ul' => array()));

		$result = $this->pagination->create(100, array('start' => false));
		$this->assertEqual($this->pagination, $result);

		// TODO add case with object as binding
	}

}
