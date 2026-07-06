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
        if ($count_cards < 3 || $count_cards > 4) {
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
        return CardUtils::isSequence($cards) || CardUtils::isGroup($cards);
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
    public static function sortCards(array $cards): array {
        $colors = array("S" => array(), "C" => array(), "H" => array(), "D" => array());
        $jokerCount = CardUtils::jokerCount($cards);
        foreach ($cards as $card) {
            $colors[$card->suit->value][] = $card;
        }
        $colors["D"] =CardUtils::sortCardsByRank($colors["D"]);
        $colors["C"] =CardUtils::sortCardsByRank($colors["C"]);
        $colors["H"] = CardUtils::sortCardsByRank($colors["H"]);
        $colors["S"] =CardUtils::sortCardsByRank($colors["S"]);

        $res = array();
        foreach($colors["D"] as $card) {
            $res[] = $card;
        }
        foreach($colors["C"] as $card) {
            $res[] = $card;
        }
        foreach($colors["H"] as $card) {
            $res[] = $card;
        }
        foreach($colors["S"] as $card) {
            $res[] = $card;
        }
        for ($i=0; $i<$jokerCount; $i++) {
            $res[] = new PlayingCard(suit: Suit::Unknown, rank: Rank::Joker);
        }
        return $res;
    }

    /**
     * @param list<PlayingCard> $cards
     */
    public static function uniqueCards(array $cards): array {
        $res = [];
        foreach($cards as $card) {
            $found = false;
            foreach($res as $uniqueCard) {
                if ($card->equals($uniqueCard)) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $res[] = $card;
            }
        }
        return $res;
    }

    /**
     * @param list<PlayingCard> $cards
     */
    public static function findCard(PlayingCard $targetCard, array $cards): int|false {
        $res = false;
        foreach($cards as $index => $card) {
            if ($card->equals($targetCard)) {
                $res = $index;
                break;
            }
        }  
        return $res;
    }

    /**
     * @param list<PlayingCard> $cards
     */
    public static function removeAll(array $cards, array $toRemoved): array {
        $res = array();
        foreach ($cards as $card) {
            $isFound = false;
            foreach ($toRemoved as $toRemove) {
                if ($card->equals($toRemove)) {
                    $isFound = true;
                    break;
                }
            }
            if ($isFound == false) {
                $res[] = $card;
            }
        }
        return $res;
    }

    /**
     * @param list<PlayingCard> $cards
     */
    public static function valueCards(array $cards): int {
        $res = 0;
        foreach($cards as $card) {
            switch ($card->rank) {
                case Rank::Two:
                    $res += 2;
                    break;
                case Rank::Three:
                    $res += 3;
                    break;
                case Rank::Four:
                    $res += 4;
                    break;
                case Rank::Five:
                    $res += 5;
                    break;
                case Rank::Six:
                    $res += 6;
                    break;
                case Rank::Seven:
                    $res += 7;
                    break;      
                case Rank::Eight:
                    $res += 8;
                    break;
                case Rank::Nine:
                    $res += 9;
                    break;
                case Rank::Ten:
                    $res += 10;
                    break;
                case Rank::Jack:
                    $res += 10;
                    break;
                case Rank::Queen:
                    $res += 10;     
                    break;
                case Rank::King:
                    $res += 10;
                    break;
                case Rank::Ace:
                    $res += 11;
                    break;                    
                case Rank::Joker:
                    $res += 15;
                    break;
            }
        }
        return $res;
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