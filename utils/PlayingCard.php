<?php

namespace Utils;

enum Suit:string {
  case Unknown = '';
  case Hearts = 'H'; // kier
  case Diamonds = 'D'; // karo
  case Clubs = 'C'; // trefl
  case Spades = 'S'; //pik
}

enum Rank:int {
  case Unknown = 0;
  case Two = 2;
  case Three = 3;
  case Four = 4;
  case Five = 5;
  case Six = 6;
  case Seven = 7;
  case Eight = 8;
  case Nine = 9;
  case Ten = 10;
  case Jack = 11;
  case Queen = 12;
  case King = 13;
  case Ace = 14;
  case Joker = 15;
}

class PlayingCard {
  public Suit $suit = Suit::Unknown;
  public Rank $rank = Rank::Unknown;

  public function __construct(Suit $suit, Rank $rank)
  {
    $this->suit = $suit;
    $this->rank = $rank;
  }

  public function toJSONString(): string {
    $rankStr = "";
    switch ($this->rank) {
        case Rank::Two:
            $rankStr = "2";
            break;
        case Rank::Three:
            $rankStr = "3";
            break;
        case Rank::Four:
            $rankStr = "4";
            break;
        case Rank::Five:
            $rankStr = "5";
            break;
        case Rank::Six:
            $rankStr = "6";
            break;
        case Rank::Seven:
            $rankStr = "7";
            break;
        case Rank::Eight:
            $rankStr = "8";
            break;
        case Rank::Nine:
            $rankStr = "9";
            break;
        case Rank::Ten:
            $rankStr = "10";
            break;
        case Rank::Jack:
            $rankStr = "J";
            break;
        case Rank::Queen:
            $rankStr = "Q";
            break;
        case Rank::King:
            $rankStr = "K";
            break;
        case Rank::Ace:
            $rankStr = "A";
            break;
        case Rank::Joker:
            $rankStr = "Joker";
            break;
        default:
            $rankStr = "";
            break;
    } 
    if ($this->suit != Suit::Unknown) {
        return $rankStr."".$this->suit->value;  
    } else {
        if ($rankStr == "Joker") {
            return $rankStr;
        }
    }
    return "";
  }

  public static function fromJSON(string $json) {
    if ($json == "Joker") {
        return new PlayingCard(suit: Suit::Unknown, rank: Rank::Joker);
    } else {
        if (strlen($json) == 2) {
            $rankStr = substr($json, 0, 1);
            $suitStr = substr($json, 1, 1);
            $suit = Suit::from($suitStr);
            $rank = Rank::Unknown;
            switch($rankStr) {
                case "2":
                    $rank = Rank::Two;
                case "3":
                    $rank = Rank::Three;
                case "4":
                    $rank = Rank::Four;
                case "5":
                    $rank = Rank::Five;
                case "6":
                    $rank = Rank::Six;
                case "7":
                    $rank = Rank::Seven;
                case "8":
                    $rank = Rank::Eight;
                case "9":
                    $rank = Rank::Nine;
                case "J":
                    $rank = Rank::Jack;
                case "Q":
                    $rank = Rank::Queen;
                case "K":
                    $rank = Rank::King;
                case "A":
                    $rank = Rank::Ace;
                default:
                    $rank = Rank::Unknown;
            }
            return new PlayingCard(suit: $suit, rank: $rank);
        } else if (strlen($json) == 3) {
            $rankStr = substr($json, 0, 2);
            $suitStr = substr($json, 2, 1);
            $suit = Suit::from($suitStr);
            if ($rankStr == "10") {
                return new PlayingCard(suit: $suit, rank: Rank::Ten);
            } else {
                return new PlayingCard(suit: $suit, rank: Rank::Unknown);
            }
        } else {
            return new PlayingCard(suit: Suit::Unknown, rank: Rank::Unknown);
        }
    }
  } 
  
  static function generateDeck(): array {
    $cards = [];
    for ($i=0; $i<2; $i++) {
        foreach (Suit::cases() as $suit) {
            if ($suit == Suit::Unknown) {
                continue;
            }
            foreach (Rank::cases() as $rank) {
                if ($rank == Rank::Unknown || $rank == Rank::Joker) {
                    continue;
                }
                $cards[] = new PlayingCard(suit: $suit, rank: $rank);
            }
        }
    }
    for ($i=0; $i<5; $i++) {
        $cards[] = new PlayingCard(suit: Suit::Unknown, rank: Rank::Joker);
    }
    return $cards;
  }
}