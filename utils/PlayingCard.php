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

 public function equals(PlayingCard $card): bool {
    return $this->suit == $card->suit && $this->rank == $card->rank;
  }

  public function equalsSuit(PlayingCrd $card): bool {
    return $this->suit == $card->suit;
  }

  public function equalsRank(PlayingCard $card): bool {
    return $this->rank == $card->rank;
  }

  public function isRankLower(PlayingCard $card): bool {
    return $this->rank->value < $card->rank->value;
  }

  public function isRankHigher(PlayingCard $card): bool {
    return $this->rank->value > $card->rank->value;
  }

  public function isSuitLower(PlayingCard $card): bool {
    switch ($this->suit) {
        case Suit::Clubs:
            return false;
        case Suit::Diamonds:
            if ($card->suit == Suit::Clubs) {
                return true;
            } else {
                return false;
            }
        case Suit::Hearts:
            if ($card->suit == Suit::Clubs || $card->suit == Suit::Diamonds) {
                return true;
            } else {
                return false;
            }
        case Suit::Spades:
            if ($cards->suit != Suit::Spades) {
                return true;
            } else {
                return false;
            }
        default:
            return false;
    }
  }

  public function isSuitHigher(PlayingCard $card): bool {
    switch ($this->suit) {
        case Suit::Clubs:
            if ($cards-suit != Suit::Clubs) {
                return true;
            } else {
                return false;
            }
        case Suit::Diamonds:
            if ($card->suit == Suit::Spades || $card->suit == Suit::Hearts) {
                return true;
            } else {
                return false;
            }
        case Suit::Hearts:
            if ($card->suit == Suit::Spades) {
                return true;
            } else {
                return false;
            }
        case Suit::Spades:
            return false;
        default:
            return false;
    }
  }

  public function next(): PlayingCard | null {
    if ($this->rank == Rank::Joker) {
        return null;
    } else {
        switch ($this->rank) {
            case Rank::Two:
                return new PlayingCard(suit: $this->suit, rank: Rank::Three);
            case Rank::Three:
                return new PlayingCard(suit: $this->suit, rank: Rank::Four);
            case Rank::Four:
                return new PlayingCard(suit: $this->suit, rank: Rank::Five);
            case Rank::Five:
                return new PlayingCard(suit: $this->suit, rank: Rank::Six);
            case Rank::Six:
                return new PlayingCard(suit: $this->suit, rank: Rank::Seven);
            case Rank::Seven:
                return new PlayingCard(suit: $this->suit, rank: Rank::Eight);
            case Rank::Eight:
                return new PlayingCard(suit: $this->suit, rank: Rank::Nine);
            case Rank::Nine:
                return new PlayingCard(suit: $this->suit, rank: Rank::Ten);
            case Rank::Ten:
                return new PlayingCard(suit: $this->suit, rank: Rank::Jack);
            case Rank::Jack:
                return new PlayingCard(suit: $this->suit, rank: Rank::Queen);
            case Rank::Queen:
                return new PlayingCard(suit: $this->suit, rank: Rank::King);
            case Rank::King:
                return new PlayingCard(suit: $this->suit, rank: Rank::Ace);
            case Rank::Ace:
                return new PlayingCard(suit: $this->suit, rank: Rank::Two);
            default:
                return null;
        }
    }
  }

  public function isNext(PlayingCard $card): bool {
    if ($this->rank == Rank::Joker) {
      return true;
    } else {
      if ($card->rank == Rank::Joker) {
        return true;
      } else {
        if (!$this->next()->equals($card)) {
          return false;
        } else {
          return true;
        }
      }
    }
  }

  public function before(): PlayingCard | null {
    if ($this->rank == Rank::Joker) {
        return null;
    } else {
        switch ($this->rank) {
            case Rank::Two:
                return new PlayingCard(suit: $this->suit, rank: Rank::Ace);
            case Rank::Three:
                return new PlayingCard(suit: $this->suit, rank: Rank::Two);
            case Rank::Four:
                return new PlayingCard(suit: $this->suit, rank: Rank::Three);
            case Rank::Five:
                return new PlayingCard(suit: $this->suit, rank: Rank::Four);
            case Rank::Six:
                return new PlayingCard(suit: $this->suit, rank: Rank::Five);
            case Rank::Seven:
                return new PlayingCard(suit: $this->suit, rank: Rank::Six);
            case Rank::Eight:
                return new PlayingCard(suit: $this->suit, rank: Rank::Seven);
            case Rank::Nine:
                return new PlayingCard(suit: $this->suit, rank: Rank::Eight);
            case Rank::Ten:
                return new PlayingCard(suit: $this->suit, rank: Rank::Nine);
            case Rank::Jack:
                return new PlayingCard(suit: $this->suit, rank: Rank::Ten);
            case Rank::Queen:
                return new PlayingCard(suit: $this->suit, rank: Rank::Jack);
            case Rank::King:
                return new PlayingCard(suit: $this->suit, rank: Rank::Queen);
            case Rank::Ace:
                return new PlayingCard(suit: $this->suit, rank: Rank::King);
            default:
                return null;
        }
    }
  }

  public function isBefore(PlayingCard $card): bool {
   if ($this->rank == Rank::Joker) {
      return true;
    } else {
      if ($card->rank == Rank::Joker) {
        return true;
      } else {
        if (!$this->before()->equals($card)) {
          return false;
        } else {
          return true;
        }
      }
    }
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