<?php

namespace Drupal\card_grid\Controller;

use Drupal;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\card_grid\Service\CardGridService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CardGridController
 */
class CardGridController extends ControllerBase
{

  /**
   *
   */
  public function get(): JsonResponse
  {
    $success = false;
    $meta = [];
    $data = [];
    $status = Response::HTTP_BAD_REQUEST;

    $rows = filter_var(Drupal::request()->query->get('rows'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
    $columns = filter_var(Drupal::request()->query->get('columns'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

    if ("" === $message = CardGridService::validate($rows, $columns)) {
      $success = true;
      $status = Response::HTTP_OK;
      $shuffledDeck = CardGridService::shuffleDeck($rows, $columns);

      $meta['success'] = $success;
      $meta['cardCount'] = $shuffledDeck['cardCount'];
      $meta['uniqueCardCount'] = $shuffledDeck['uniqueCardCount'];
      $meta['uniqueCards'] = $shuffledDeck['uniqueCards'];
      $data['cards'] = $shuffledDeck['cards'];
    } else {
      $meta['success'] = $success;
      $meta['message'] = $message;
    }

    return new JsonResponse(
      [
        'meta' => $meta,
        'data' => $data
      ],
      $status);
  }
}
