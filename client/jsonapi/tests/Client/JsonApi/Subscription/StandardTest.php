<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\Client\JsonApi\Subscription;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		$this->context = \TestHelperJapi::getContext();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Client\JsonApi\Subscription\Standard( $this->context, 'subscription' );
		$this->object->setView( $this->view );
	}


	protected function tearDown()
	{
		\Aimeos\Controller\Frontend\Subscription\Factory::injectController( '\Aimeos\Controller\Frontend\Subscription\Standard', null );
		unset( $this->context, $this->object, $this->view );
	}


	public function testCancel()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'subscription' );
		$search = $manager->createSearch()->setSlice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'subscription.dateend', '2010-01-01' ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No subscription item found' );
		}

		$params = array( 'id' => $item->getId() );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );


		$object = $this->getObject( 'cancel', $this->returnValue( $item ) );

		$response = $object->delete( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Allow' ) ) );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 'subscription', $result['data']['type'] );
		$this->assertGreaterThan( 4, count( $result['data']['attributes'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testCancelControllerException()
	{
		$object = $this->getObject( 'cancel', $this->throwException( new \Aimeos\Controller\Frontend\Exception() ) );

		$response = $object->delete( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testCancelMShopException()
	{
		$object = $this->getObject( 'cancel', $this->throwException( new \Aimeos\MShop\Exception() ) );

		$response = $object->delete( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 404, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testCancelException()
	{
		$object = $this->getObject( 'cancel', $this->throwException( new \Exception() ) );

		$response = $object->delete( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 500, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testGet()
	{
		$user = \Aimeos\MShop::create( $this->context, 'customer' )->findItem( 'UTC001' );
		$this->context->setUserId( $user->getId() );

		$params = array( 'fields' => array( 'subscription' => 'subscription.id,subscription.datenext,subscription.dateend' ) );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );


		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Allow' ) ) );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 2, $result['meta']['total'] );
		$this->assertEquals( 'subscription', $result['data'][0]['type'] );
		$this->assertEquals( 3, count( $result['data'][0]['attributes'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetById()
	{
		$user = \Aimeos\MShop::create( $this->context, 'customer' )->findItem( 'UTC001' );
		$this->context->setUserId( $user->getId() );

		$manager = \Aimeos\MShop::create( $this->context, 'subscription' );
		$search = $manager->createSearch()->setSlice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'subscription.dateend', '2010-01-01' ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No subscription item found' );
		}

		$params = array( 'id' => $item->getId() );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );


		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Allow' ) ) );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 'subscription', $result['data']['type'] );
		$this->assertGreaterThan( 4, count( $result['data']['attributes'] ) );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetControllerException()
	{
		$object = $this->getObject( 'createFilter', $this->throwException( new \Aimeos\Controller\Frontend\Exception() ) );

		$response = $object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testGetMShopException()
	{
		$object = $this->getObject( 'createFilter', $this->throwException( new \Aimeos\MShop\Exception() ) );

		$response = $object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 404, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testGetException()
	{
		$object = $this->getObject( 'createFilter', $this->throwException( new \Exception() ) );

		$response = $object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 500, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testOptions()
	{
		$response = $this->object->options( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Allow' ) ) );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( null, $result['meta']['prefix'] );
		$this->assertEquals( 0, count( $result['meta']['attributes'] ) );
		$this->assertArrayNotHasKey( 'filter', $result['meta'] );
		$this->assertArrayNotHasKey( 'sort', $result['meta'] );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	/**
	 * Returns a test object with a mocked subscription controller
	 *
	 * @param string $method Subscription controller method name to mock
	 * @param mixed $result Return value of the mocked method
	 */
	protected function getObject( $method, $result )
	{
		$cntl = $this->getMockBuilder( \Aimeos\Controller\Frontend\Subscription\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( [$method] )
			->getMock();

		$cntl->expects( $this->once() )->method( $method )->will( $result );

		\Aimeos\Controller\Frontend\Subscription\Factory::injectController( '\Aimeos\Controller\Frontend\Subscription\Standard', $cntl );

		$object = new \Aimeos\Client\JsonApi\Subscription\Standard( $this->context, 'subscription' );
		$object->setView( $this->view );


		return $object;
	}
}