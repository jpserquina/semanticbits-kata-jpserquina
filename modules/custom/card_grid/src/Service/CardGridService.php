<?php

namespace Drupal\card_grid\Service;

/**
 * Class CardGridService
 */
class CardGridService
{
  public const LOWER_BOUND_CARD_GRID = 0;
  public const UPPER_BOUND_CARD_GRID = 6;
  public const DEFAULT_DECK_VALUES = ["A01", "B02", "C03", "D04", "E05", "F06", "G07", "H08", "I09", "J10", "K11", "L12", "M13",
    "N14", "O15", "P16", "Q17", "R18", "S19", "T20", "U21", "V22", "W23", "X24", "Y25", "Z26", "A27", "B28", "C29", "D30",
    "D31", "E32", "F33", "G34", "H35", "I36"];

  public const NEWLINE = "\n";
  public const ERROR_INCORRECT_INPUT = "Either `rows` or `columns` needs to be a number.";
  public const ERROR_NOT_EVEN_NUMBER = "Either `rows` or `columns` needs to be an even number.";
  public const ERROR_COUNT_GREATER_THAN = " count is greater than " . self::UPPER_BOUND_CARD_GRID . ".";
  public const ERROR_COUNT_LESS_THAN = " count is less than " . self::LOWER_BOUND_CARD_GRID . ".";


  /**
   * @param int|null $rows
   * @param int|null $columns
   * @return string
   */
  public static function validate(?int $rows, ?int $columns): string
  {
    $result = "";

    if ($rows === null || $columns === null) {
      $result .= self::ERROR_INCORRECT_INPUT . self::NEWLINE;
    }

    if ($rows % 2 > 0 && $columns % 2 > 0) {
      $result .= self::ERROR_NOT_EVEN_NUMBER . self::NEWLINE;
    }

    if ($rows < self::LOWER_BOUND_CARD_GRID) {
      $result .= "Row" . self::ERROR_COUNT_LESS_THAN . self::NEWLINE;
    }

    if ($rows > self::UPPER_BOUND_CARD_GRID) {
      $result .= "Row" . self::ERROR_COUNT_GREATER_THAN . self::NEWLINE;
    }

    if ($columns < self::LOWER_BOUND_CARD_GRID) {
      $result .= "Column" . self::ERROR_COUNT_LESS_THAN . self::NEWLINE;
    }

    if ($columns > self::UPPER_BOUND_CARD_GRID) {
      $result .= "Column" . self::ERROR_COUNT_GREATER_THAN . self::NEWLINE;
    }

    return substr($result, 0, -1);
  }

  /**
   * @param int $rows
   * @param int $columns
   * @param array $deck
   * @return array
   */
  public static function shuffleDeck(int $rows, int $columns, array $deck = []): array
  {
    if (empty($deck)) {
      $deck = self::DEFAULT_DECK_VALUES;
    }

    $totalNumber = $rows * $columns;
    $uniqueTotalNumber = $totalNumber / 2;

    $uniqueShuffledDeck = self::enhanced_array_rand($deck, $uniqueTotalNumber);
    $shuffledAndCutDeck = self::splitDeckIntoGroups($columns, $uniqueShuffledDeck);

    return [
      'cards' => $shuffledAndCutDeck,
      'cardCount' => $totalNumber,
      'uniqueCardCount' => $uniqueTotalNumber,
      'uniqueCards' => $uniqueShuffledDeck
    ];
  }

  /**
   * @param array|null $array $array
   * @param int $uniqueTotalNumber
   * @return array
   */
  private static function enhanced_array_rand(?array $array, int $uniqueTotalNumber): array
  {
    shuffle($array);

    return array_slice($array, 0, $uniqueTotalNumber);
  }

  /**
   * @param int $columns
   * @param array $deck
   * @return array
   */
  private static function splitDeckIntoGroups(int $columns, array $deck): array
  {
    $dupedDeck = array_merge($deck, $deck);
    shuffle($dupedDeck);

    // TODO - guard check the uniqueness per chunk

    return array_chunk($dupedDeck, $columns);
  }
}
