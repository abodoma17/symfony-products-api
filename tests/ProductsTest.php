<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ProductsTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => '/api/products',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/products?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/products?page=1',
                'hydra:last' => '/api/products?page=20',
                'hydra:next' => '/api/products?page=2',
            ],
        ]);

        $this->assertCount(5, $response->toArray()['hydra:member']);
    }

    public function testPagination(): void
    {
        $response = static::createClient()->request('GET', '/api/products?page=2');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => '/api/products',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/products?page=2',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/products?page=1',
                'hydra:last' => '/api/products?page=20',
                'hydra:previous' => '/api/products?page=1',
                'hydra:next' => '/api/products?page=3',
            ],
        ]);
    }

    public function testCreateProduct(): void
    {
        $response = static::createClient()->request('POST', '/api/products', [
            'json' => [
                'mpn' => '1234',
                'name' => 'A Test Product!',
                'description' => 'A Test Product Description',
                'issueDate' => '1985-07-20',
                'manufacturer' => '/api/manufacturers/1',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json'
            ]
        ]);

        $this->assertResponseStatusCodeSame('201');
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            'mpn' => '1234',
            'name' => 'A Test Product!',
            'description' => 'A Test Product Description',
            'issueDate' => '1985-07-20T00:00:00+00:00',
        ]);
    }

    public function testUpdateProduct():void
    {
        $response = static::createClient()->request('PATCH', 'api/products/1', [
            'json' => [
                'description' => 'An updated description',
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => '/api/products/1',
            'description' => 'An updated description'
        ]);
    }

    public function testCreateInvalidProduct():void
    {
        $response = static::createClient()->request('POST', 'api/products', [
            'json' => [
                'mpn' => '1234',
                'name' => 'A Test Product!',
                'description' => 'A Test Product Description',
                'issueDate' => '1985-07-20',
                'manufacturer' => null
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@type'             => 'ConstraintViolationList',
            'hydra:title'       => 'An error occurred',
            'hydra:description' => 'manufacturer: This value should not be null.',
        ]);
    }
}