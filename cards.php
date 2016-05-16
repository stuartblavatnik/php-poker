<?
/*
    Module:             cards.php

    Description:        functions that operate on cards

    Functions:          get_card_description()
                        get_single_card_description()
                        get_rank()
                        get_rank_description()
                        get_suit_description()
                        get_plural_rank_description()
                        get_suit()


*/

/*
    Function:       GetCardDescription()
    
    Parameters:     cardNumber (value from 0 - 51)

    Description:    Returns a string representation of rank and suit for
                    a card

    Returns:        String
*/

function get_card_description($cardNumber)
{
    $rankdescription = get_rank_description(get_rank($cardNumber));
    $suitdescription = get_suit_description(get_suit($cardNumber));

    return $rankdescription . " of " . $suitdescription;
}

/*
    Function:       get_single_card_description()
    
    Parameters:     raw cardNumber (value from 0 - 51)

    Description:    Returns a string representation of rank 
                    a card

    Returns:        String
*/

function get_single_card_description($cardNumber)
{
    $rankdescription = get_rank_description(get_rank($cardNumber));

    return $rankdescription;
}

/*
    Function:       get_rank()

    Parameters:     cardNumber

    Description:    Returns the rank as a constant

    Returns:        int   
*/

function get_rank($cardNumber)
{
    return intval(floor($cardNumber / NUMBER_OF_SUITS));
}

/*
    Function:       get_rank_description()

    Parameters:     cardNumber -- processed card number - 12

    Description:    Retrieves the card rank as a string

    Returns:        Card rank as string
*/

function get_rank_description($cardNumber)
{
   $ranks = array("Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Jack", "Queen", "King", "Ace");
 
    $rank_description = $ranks[$cardNumber];

    return $rank_description;
}

/*
    Function:       get_suit_description()

    Parameters:     cardNumber -- processed card suit 0 - 3

    Description:    Retrieves the suit as string

    Returns:        String value of card
*/

function get_suit_description($cardNumber)
{
    $suits = array("Clubs", "Diamonds", "Hearts", "Spades" );
    $suit_description = $suits[$cardNumber];
    return $suit_description;
}

/*
    Function:       get_plural_rank_description()

    Parameters:     cardNumber -- processed card number (0 - 12)

    Description:    Retrieves the plural version of the card rank

    Returns:        Card rank as string
*/

function get_plural_rank_description($cardNumber)
{
   $ranks = array("Twos", "Threes", "Fours", "Fives", "Sixes", "Sevens", "Eights", "Nines", "Tens", "Jacks", "Queens", "Kings", "Aces");
 
    $rank_description = $ranks[$cardNumber];

    return $rank_description;
}

/*
    Function:       get_suit()

    Parameters:     cardNumber

    Description:    Returns the suit as a constant

    Returns:        int   
*/

function get_suit($cardNumber)
{
    return $cardNumber % NUMBER_OF_SUITS;
}

?>