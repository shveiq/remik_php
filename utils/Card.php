<?php

enum Suit:string {
  case Unknown = "";
  case Hearts = "♥"; // kier
  case Diamonds = "♦"; // karo
  case Clubs = "♣"; // trefl
  case Spades = "♠"; //pik
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
  Suit suit;
  Rank rank;

  public toJSONString() {
    rankStr = "";
    switch (rank) {
        case Rank::Two:
            rankStr += "2";
        case Rank::Three:
            rankStr += "3";
        case Rank::Four:
            rankStr += "4";
        case Rank::Five:
            rankStr += "5";
        case Rank::Six:
            rankStr += "6";
        case Rank::Seven:
            rankStr += "7";
        case Rank::Eight:
            rankStr += "8";
        case Rank::Nine:
            rankStr += "9";
        case Rank::Ten:
            rankStr += "10";
        case Rank::Jack:
            rankStr = "J";
        case Rank::Queen:
            rankStr = "Q";
        case Rank::King:
            rankStr = "K";
        case Rank::Ace:
            rankStr = "A";
        case Rank::Joker:
            rankStr = "Joker";
    } 
    return rankStr."".suit->value;  
  }

  public static PlayingCard.fromJSON(string json) {
    if (json == "Joker") {
        return PlayingCard(rank: Rank::Joker);
    } else {
        if (strlen(json) == 2) {
            rankStr = substr(json, 0, 1);
            suitStr = substr(json, 1, 1);
            suit = Suit::from(suitStr);
            rank = Rank::Unknown;
            switch(rankStr) {
                case "2":
                    rank = Rank::Two;
                case "3":
                    rank = Rank::Three;
                case "4":
                    rank = Rank::Four;
                case "5":
                    rank = Rank::Five;
                case "6":
                    rank = Rank::Six;
                case "7":
                    rank = Rank::Seven;
                case "8":
                    rank = Rank::Eight;
                case "9":
                    rank = Rank::Nine;
                case "J":
                    rank = Rank::Jack;
                case "Q":
                    rank = Rank::Queen;
                case "K":
                    rank = Rank::King;
                case "A":
                    rank = Rank::Ace;
                default:
                    rank = Rank::Unknown;
            }
            return PlayingCard(suit: suit, rank: rank);
        } else if (strlen(json) == 3) {
            suitStr = substr(json, 2, 1);
            suit = Suit::from(suitStr);
            if (rankStr == "10") {
                return PlayingCard(suit: suit, rank: Rank::Ten);
            } else {
                return PlayingCard(suit: suit, rank: Rank.Unknown);
            }
        } else {
            return PlayingCard(suit: Suit.Unknown, rank: Rank.Unknown);
        }
    }
  }     
}