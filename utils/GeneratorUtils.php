<?php

namespace Utils;

class GeneratorUtils
{
    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param list<PlayingCard> $cards
     */
    public static function generateAllCardGroups(array $cards): array {
        $groups = [];
        $jokerCount = CardUtils::jokerCount($cards);

        foreach ($cards as $card) {
            if ($card->rank == Rank::Unknown || $card->rank == Rank::Joker) {
                continue;
            }
            $groups[$card->rank->value][] = $card;
        }

        $melds = [];

        foreach ($groups as $sameValue) {
            $isFound = false;
            $count = count($sameValue);
            if (CardUtils::isGroup($sameValue)) {
                $melds[] = $sameValue;
                $isFound = true;
                if ($count == 4) {
                    // dodaj wszystkie kombinacje 3 kart z 4
                    $melds[] = [$sameValue[0], $sameValue[1], $sameValue[2]];
                    $melds[] = [$sameValue[0], $sameValue[1], $sameValue[3]];
                    $melds[] = [$sameValue[0], $sameValue[2], $sameValue[3]];
                    $melds[] = [$sameValue[1], $sameValue[2], $sameValue[3]];   
                }
            } else if ($count == 2 && $jokerCount > 0) {
                $melds[] = array_merge($sameValue, [new PlayingCard(rank: Rank::Joker, suit: Suit::Unknown)]);
                $isFound = true;
            }
            if ($isFound == false) {
                if ($count > 2) {
                    $subSameValue = CardUtils::uniqueCards($sameValue);
                    $newCount = count($subSameValue);
                    if (CardUtils::isGroup($subSameValue)) {
                        $melds[] = $subSameValue;
                        $isFound = true;
                    } else if ($newCount == 2 && $jokerCount > 0) {
                        $melds[] = array_merge($subSameValue, [new PlayingCard(rank: Rank::Joker, suit: Suit::Unknown)]);
                        $isFound = true;
                    }
                }
            }
        }

        return $melds;
    }

    /**
     * @param list<PlayingCard> $cards
     */
    public static function generateAllCardSequence(array $cards): array {
        $suits = [];
        $jokerCount = CardUtils::jokerCount($cards);

        foreach ($cards as $card) {
            if ($card->rank == Rank::Unknown || $card->rank == Rank::Joker) {
                continue;
            }
            $suits[$card->suit->value][] = $card;
        }

        $sequences = [];

        foreach ($suits as $suit => $sameSuit) {
            $sameSuit = CardUtils::sortCardsByRank($sameSuit);
            if (CardUtils::findCard(new PlayingCard(rank: Rank::Ace, suit: Suit::from($suit)), $sameSuit)) {
                array_unshift($sameSuit, new PlayingCard(rank: Rank::Ace, suit: Suit::from($suit)));
            }
            for ($i=0; $i<count($sameSuit)-2; $i++) {
                $sequence = [$sameSuit[$i]];
                $index = $i+1;
                while ($index < count($sameSuit)) {
                    $beforeCard = $sameSuit[$index-1]->next();
                    $isNext = $sameSuit[$index]->isBefore($sequence[count($sequence)-1]);
                    if ($isNext) {
                        $sequence[] = $sameSuit[$index];
                    } else if ($jokerCount > 0 && $beforeCard != null && $sameSuit[$index]->isBefore($beforeCard)) {
                        $sequence[] = new PlayingCard(rank: Rank::Joker, suit: Suit::Unknown);
                        $sequence[] = $sameSuit[$index];
                    } else if (count($sequence) >= 3) {
                        break;
                    } else {
                        break;
                    }
                    $index ++;
                }
                if (count($sequence) >= 3) {
                    if (CardUtils::isSequence($sequence)) {
                        $sequences[] = $sequence;
                    }
                }
            }
        }

        return $sequences;
    }
    
    /**
     * @param list<PlayingCard> $cards
     */    
    public static function generateAllMelds(array $cards): array
    {
        return array_merge(
            GeneratorUtils::generateAllCardGroups($cards),
            GeneratorUtils::generateAllCardSequence($cards)
        );
    }

    /**
     *  @param list<PlayingCard> $cards
     */
    public static function bestMelds(array $cards): array
    {
        $bestMelds = GeneratorUtils::generateAllMelds($cards);
        if (count($bestMelds)>0) {
            $bestMeldValue = 0;
            $bestMeld = null;
            foreach ($bestMelds as $meld) {
                $meldValue = CardUtils::valueCards($meld);
                if ($meldValue > $bestMeldValue) {
                    $bestMeld = $meld;
                    $bestMeldValue = $meldValue;
                }
            }
            $res = GeneratorUtils::bestMelds(CardUtils::removeAll($cards, $bestMeld));
            $res[] = $bestMeld;
            return $res;
        }
        return [];
    }

}