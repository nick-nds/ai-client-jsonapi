<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */


namespace Aimeos\Client;


class JsonApiTest extends \PHPUnit\Framework\TestCase
{
	public function testCreate()
	{
		$context = \TestHelperJapi::context();

		$client = \Aimeos\Client\JsonApi::create( $context, 'product' );
		$this->assertInstanceOf( \Aimeos\Client\JsonApi\Iface::class, $client );
	}


	public function testCreateEmpty()
	{
		$context = \TestHelperJapi::context();

		$client = \Aimeos\Client\JsonApi::create( $context, '' );
		$this->assertInstanceOf( \Aimeos\Client\JsonApi\Iface::class, $client );
	}


	public function testCreateInvalidPath()
	{
		$context = \TestHelperJapi::context();

		$this->expectException( \Aimeos\Client\JsonApi\Exception::class );
		\Aimeos\Client\JsonApi::create( $context, '%^' );
	}


	public function testCreateInvalidName()
	{
		$context = \TestHelperJapi::context();

		$this->expectException( \Aimeos\Client\JsonApi\Exception::class );
		\Aimeos\Client\JsonApi::create( $context, '', '%^' );
	}
}
