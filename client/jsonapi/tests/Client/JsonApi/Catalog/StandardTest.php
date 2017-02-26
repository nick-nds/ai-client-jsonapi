<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */


namespace Aimeos\Client\JsonApi\Catalog;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		$this->context = \TestHelperJapi::getContext();
		$templatePaths = \TestHelperJapi::getTemplatePaths();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Client\JsonApi\Catalog\Standard( $this->context, $this->view, $templatePaths, 'catalog' );
	}


	public function testGetItem()
	{
		$catId = \Aimeos\MShop\Factory::createManager( $this->context, 'catalog' )->findItem( 'cafe' )->getId();
		$params = array(
			'id' => $catId,
			'fields' => array(
				'catalog' => 'catalog.id,catalog.label'
			),
			'sort' => 'catalog.id',
			'include' => 'catalog,media,text'
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Allow' ) ) );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 'catalog', $result['data']['type'] );
		$this->assertEquals( 1, count( $result['data']['attributes']['text'] ) );
		$this->assertEquals( 2, count( $result['data']['attributes']['media'] ) );
		$this->assertEquals( 0, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetItemNoID()
	{
		$params = array(
			'filter' => array( '>=' => array( 'catalog.level' => 0 ) ),
			'include' => 'catalog'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Allow' ) ) );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 'catalog', $result['data']['type'] );
		$this->assertEquals( 'root', $result['data']['attributes']['catalog.code'] );
		$this->assertEquals( 'Root', $result['data']['attributes']['catalog.label'] );
		$this->assertEquals( 2, count( $result['data']['relationships']['catalog']['data'] ) );
		$this->assertEquals( 'catalog', $result['data']['relationships']['catalog']['data'][0]['type'] );
		$this->assertEquals( 2, count( $result['included'] ) );
		$this->assertArrayHaskey( 'self', $result['included'][0]['links'] );

		$this->assertArrayNotHasKey( 'errors', $result );
	}
}