<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017
 */


namespace Aimeos\Client\JsonApi\Attribute;


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

		$this->object = new \Aimeos\Client\JsonApi\Attribute\Standard( $this->context, $this->view, $templatePaths, 'attribute' );
	}


	public function testGetItem()
	{
		$attrManager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' );
		$attrId = $attrManager->findItem( 'xs', [], 'product', 'size' )->getId();

		$params = array(
			'id' => $attrId,
			'fields' => array(
				'attribute' => 'attribute.id,attribute.label'
			),
			'sort' => 'attribute.id',
			'include' => 'media,price,text'
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Allow' ) ) );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 'attribute', $result['data']['type'] );
		$this->assertEquals( 3, count( $result['data']['attributes']['text'] ) );
		$this->assertEquals( 1, count( $result['data']['attributes']['price'] ) );
		$this->assertEquals( 1, count( $result['data']['attributes']['media'] ) );
		$this->assertEquals( 0, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetItems()
	{
		$params = array(
			'fields' => array(
				'attribute' => 'attribute.id,attribute.label'
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Allow' ) ) );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 24, $result['meta']['total'] );
		$this->assertEquals( 24, count( $result['data'] ) );
		$this->assertEquals( 'attribute', $result['data'][0]['type'] );
		$this->assertEquals( 2, count( $result['data'][0]['attributes'] ) );
		$this->assertEquals( 0, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetItemsCriteria()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'attribute.type.code' => 'size' ),
			),
			'sort' => 'attribute.position',
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 6, $result['meta']['total'] );
		$this->assertArrayNotHasKey( 'errors', $result );
	}
}