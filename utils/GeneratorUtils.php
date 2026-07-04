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
            $count = count($sameValue);
            if (CardUtils::isGroup($sameValue)) {
                $melds[] = $sameValue;
            }
            if ($count == 2 && $jokerCount > 0) {
                $melds[] = array_merge($sameValue, [new PlayingCard(rank: Rank::Joker, suit: Suit::Unknown)]);
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

        foreach ($suits as $sameSuit) {
            usort($sameSuit, function($a, $b) {
                return $a->rank->value - $b->rank->value;
            });

            for ($i=0; $i<count($sameSuit)-2; $i++) {
                $sequence = [$sameSuit[$i]];
                $index = 1;
                while ($index < count($sameSuit)) {
                    $afterJoker = $sequence[count($sequence)-1]->next();
                    if ($sameSuit[$index]->isNext($sequence[count($sequence)-1])) {
                        $sequence[] = $sameSuit[$index];
                    } else if ($jokerCount > 0 && $afterJoker != null && $sameSuit[$index]->isNext($afterJoker)) {
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

}