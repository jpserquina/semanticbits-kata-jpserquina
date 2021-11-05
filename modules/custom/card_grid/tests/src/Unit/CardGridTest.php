<?php

namespace Drupal\Tests\card_grid\Unit;

use Drupal\card_grid\Service\CardGridService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CardGridTest
 */
class CardGridTest extends TestCase
{

  const BASE_URI = 'https://semanticbits-kata-jpserquina.lndo.site';
  //  const BASE_URI = 'http://localhost:8000';
  const ENDPOINT = "/code-challenge/card-grid";
  private Client $client;

  /**
   * Setup before running each test case
   */
  public function setUp(): void
  {
    $this->client = new Client([
      'base_uri' => self::BASE_URI,
      'verify' => false
    ]);
    parent::setUp();
  }

  /**
   * Test for HTTP 200 and response payload shape
   * @throws GuzzleException
   */
  public function testHttp200HasCompleteStructure()
  {
    $payload = [
      'query' => [
        'rows' => CardGridService::UPPER_BOUND_CARD_GRID,
        'columns' => CardGridService::UPPER_BOUND_CARD_GRID
      ]
    ];
    $response = $this->client->request('GET', self::ENDPOINT, $payload);
    $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

    $body = json_decode($response->getBody(), true);
    $totalExpectedCards = CardGridService::UPPER_BOUND_CARD_GRID * CardGridService::UPPER_BOUND_CARD_GRID;
    $this->assertArrayHasKey('meta', $body);
    $this->assertArrayHasKey('data', $body);
    $this->assertCount(
      $totalExpectedCards,
      $body['data']['cards']
    );
    $this->assertEquals(true, $body['meta']['success']);
    $this->assertEquals($totalExpectedCards, $body['meta']['cardCount']);
    $this->assertNotNull($body['meta']['uniqueCardCount']);
    $uniqueCardCount = $body['meta']['uniqueCardCount'];
    $this->assertCount(
      $uniqueCardCount,
      $body['data']['uniqueCards']
    );
  }

  /**
   * Test for out of bounds error
   * @throws GuzzleException
   */
  public function testOutOfLowerBoundsRowAndColumnHttp400()
  {
    $payload = [
      'query' => [
        'rows' => CardGridService::LOWER_BOUND_CARD_GRID - 1,
        'columns' => CardGridService::LOWER_BOUND_CARD_GRID - 1
      ]
    ];
    $response = $this->client->request('GET', self::ENDPOINT, $payload);
    $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
  }

  /**
   * Test for out of bounds error
   * @throws GuzzleException
   */
  public function testOutOfUpperBoundsRowAndColumnHttp400()
  {
    $payload = [
      'query' => [
        'rows' => CardGridService::UPPER_BOUND_CARD_GRID + 1,
        'columns' => CardGridService::UPPER_BOUND_CARD_GRID + 1
      ]
    ];
    $response = $this->client->request('GET', self::ENDPOINT, $payload);
    $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
  }

  /**
   * Test for out of bounds error
   * @throws GuzzleException
   */
  public function testOddValueRowAndColumnHttp400()
  {
    $payload = [
      'query' => [
        'rows' => CardGridService::UPPER_BOUND_CARD_GRID - 1,
        'columns' => CardGridService::UPPER_BOUND_CARD_GRID - 1
      ]
    ];
    $response = $this->client->request('GET', self::ENDPOINT, $payload);
    $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
  }
}
