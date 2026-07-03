<?php

namespace Utils;

class CardUtils {

    /**
    * @param list<PlayingCard> $cards
    */
    public static function isSequence(array $cards): bool {
        if (count($cards) >= 3) {
            $lastCard = $cards[0];
            $prevLastCard = null;
            $index = 1;
            $isCorrect = $index < count($cards);
            while($isCorrect) {
                $currentCard = $cards[$index];
                if ($currentCard->rank == Rank::Joker) {
                    $prevLastCard = $lastCard;
                    $lastCard = $lastCard->next();
                } else {
                    if (!$lastCard->isNext($currentCard)) {
                        $isCorrect = false;
                    } else {
                        if ($lastCard != null && $prevLastCard != null) {
                            if ($lastCard->rank == Rank::Ace) {
                                $isCorrect = false;
                                return $isCorrect;
                            }
                        }                        
                        $prevLastCard = $lastCard;
                        $lastCard = $currentCard;
                    }
                }
                if ($index < count($cards) - 1) {
                    if ($lastCard == null) {
                        $isCorrect = false;
                    }
                    $index ++;
                } else {
                    return $isCorrect;
                }
            }
            return $isCorrect;
        } else {
            return false;
        }
    }

    /**
    * @param list<PlayingCard> $cards
    */
    public static function isGroup(array $cards): bool {
        $count_cards = count($cards);
        if ($count_cards < 3 && $count_cards > 4) {
            return false;
        }
        if ($count_cards == 3 && CardUtils::jokerCount($cards) >= 2) {
            return false;
        }
        if ($count_cards == 4 && CardUtils::jokerCount($cards) >= 3) {
            return false;
        }
        for ($i=0; $i<count($cards)-1; $i++) {
            if ($cards[$i]->rank == Rank::Joker) {
                continue;
            }
            for ($j=$i+1; $j<count($cards); $j++) {
                if ($cards[$j]->rank == Rank::Joker) {
                    continue;
                }
                if ($cards[$i]->rank != $cards[$j]->rank) {
                    return false;
                } else if ($cards[$i]->suit == $cards[$j]->suit) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param list<PlayingCard> cards
     */
    public static function jokerCount(array $cards): int {
      $res = 0;
      foreach($cards as $card) {
        if ($card->rank == Rank::Joker) {
            $res++;
        }
      }
      return $res;
    }

    /**
    * @param list<PlayingCard> $cards
    */
    public static function isValidMeld(array $cards): bool {
        return isSequence($cards) || isGroup($cards);
    }

    /**
     * @param list<PlayingCard> $cards
     */
    public static function sortCardsByRank(array $cards): array {
        $n = count($cards);
        for($i=0; $i<$n-1; $i++) {
            $swapped = false;
            for ($j=0; $j<$n-$i-1; $j++) {
                if (!$cards[$j]->isRankLower($cards[$j+1])) {
                    $tmp = $cards[$j];
                    $cards[$j] = $cards[$j+1];
                    $cards[$j+1] = $tmp;
                    $swapped = true;
                } else {
                    if ($cards[$j]->equalsRank($cards[$j+1])) {
                        if ($cards[$j]->suit != $cards[$j+1]->suit) {

                        }
                    }
                }
            }
            if (!$swapped) {
                break;
            }
        }
        return $cards;
    }

    /**
    * @param list<PlayingCard> $cards
    */
    public static function toString(array $cards): string {
      $res = "";
      $znak = "";
      foreach($cards as $card) {
        $res .= $znak.$card->toJSONString();
        $znak = ", ";
      }
      return $res;
    }

}