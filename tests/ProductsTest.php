<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductsTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    private const API_TOKEN = '9273e47c5257ba3803d5b001b3b2677670f181595b5b95f0119077f4c7fbe860fd348731c5700c962bd0585528e031f2381bb0fe69a54bfeb28651e9';

    private HttpClientInterface $client;
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setEmail('test@test.com');
        $user->setPassword('$2y92tqb38as123');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $apiToken = new ApiToken();
        $apiToken->setToken(self::API_TOKEN);
        $apiToken->setUser($user);
        $this->entityManager->persist($apiToken);
        $this->entityManager->flush();


    }

    public function testGetCollection(): void
    {
        $response = $this
            ->client
            ->request('GET', '/api/products', [
                    'headers' => [
                        'x-api-token' => self::API_TOKEN
                    ]
                ]);

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
        $response = $this->client
            ->request('GET', '/api/products?page=2', [
                'headers' => [
                    'x-api-token' => self::API_TOKEN
                ]
            ]);

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
        $response = $this->client->request('POST', '/api/products', [
            'json' => [
                'mpn' => '1234',
                'name' => 'A Test Product!',
                'description' => 'A Test Product Description',
                'issueDate' => '1985-07-20',
                'manufacturer' => '/api/manufacturers/1',
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
                'x-api-token' => self::API_TOKEN,
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

    public function testUpdateProduct(): void
    {
        $response = $this->client->request('PATCH', 'api/products/1', [
            'json' => [
                'description' => 'An updated description',
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
                'x-api-token' => self::API_TOKEN,
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => '/api/products/1',
            'description' => 'An updated description'
        ]);
    }

    public function testCreateInvalidProduct(): void
    {
        $response = $this->client->request('POST', 'api/products', [
            'json' => [
                'mpn' => '1234',
                'name' => 'A Test Product!',
                'description' => 'A Test Product Description',
                'issueDate' => '1985-07-20',
                'manufacturer' => null
            ],
            'headers' => [
                'Content-Type' => 'application/ld+json',
                'x-api-token' => self::API_TOKEN,
            ]
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'manufacturer: This value should not be null.',
        ]);
    }

    public function testInvalidToken(): void
    {
        $response = $this->client->request('PATCH', 'api/products/1', [
            'json' => [
                'description' => 'An updated description',
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
                'x-api-token' => 'invalidToken',
            ]
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'message' => 'Invalid credentials.'
        ]);
    }
}