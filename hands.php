<?
/*
    Module:             hands.php

    Description:        functions that operate on hands

    Functions:          compare_hands() -- Compares the two hands and returns outcome constant
                        compare_hands_by_individual_values() -- Compares hands by each card ranking
                        parse_hand() -- Determines type of hand 
                        get_hand_description() -- String version of hand
                        get_ranks() -- Retrieves all of the rankings for a hand
*/
    require("consts.php");

/*
    Function:       compare_hands()

    Parameters:     dealer_hand_type -- dealer hand type ranking
                    dealer_hand      -- dealer actual hand
                    dealer_extra     -- dealer extra information
                    player_hand_type -- player hand type
                    player_hand      -- player actual hand
                    player_extra     -- player extra information

    Description:    Compares the two hands and returns outcome constant

    Returns:        Outcome constant as int
*/
function compare_hands($dealer_hand_type, 
                       $dealer_hand, 
                       $dealer_extra, 
                       $player_hand_type, 
                       $player_hand, 
                       $player_extra)
{
    $retval = TIE;

    if ($dealer_hand_type > $player_hand_type)
    {
        $retval = LOSE;
    }
    else if ($dealer_hand_type < $player_hand_type)
    {
        $retval = WIN;
    }
    else
    {
        switch ($dealer_hand_type)
        {
            case HAND_STRAIGH_FLUSH:
            case HAND_FLUSH:
            case HAND_STRAIGHT:
            case HAND_HIGH_CARD:
                $retval = compare_hands_by_individual_values($dealer_hand, $player_hand);
                break;

            case HAND_FOUR_OF_A_KIND:
            case HAND_FULL_HOUSE:
            case HAND_THREE_OF_A_KIND:
                if ($dealer_extra > $player_extra)
                {
                    $retval = LOSE;
                }
                else
                {
                    $retval = WIN;
                }
                break;
            case HAND_TWO_PAIR:
                //first comapre the high pairs
                if ($dealer_extra[0] > $player_extra[0])
                {
                    $retval = LOSE;
                }
                else if ($dealer_extra[0] < $player_extra[0])
                {
                    $retval = WIN;
                }
                else
                {
                    //next compare the low pairs
                    if ($dealer_extra[1] > $player_extra[1])
                    {
                        $retval = LOSE;
                    }
                    else if ($dealer_extra[1] < $player_extra[1])
                    {
                        $retval = WIN;
                    }
                    else
                    {
                        //finally compare the last card
                        $retval = compare_hands_by_individual_values($dealer_hand, $player_hand);
                    }
                }
                break;
            case HAND_PAIR:
                //first comapre the high pairs
                if ($dealer_extra > $player_extra)
                {
                    $retval = LOSE;
                }
                else if ($dealer_extra < $player_extra)
                {
                    $retval = WIN;
                }
                else
                {
                    //finally compare the last card
                    $retval = compare_hands_by_individual_values($dealer_hand, $player_hand);
                }
                break;
        }
    }

    return $retval;
}

/*
    Function:       compare_hands_by_individual_values()

    Parameters:     dealer_hand
                    player_hand

    Description:    Sorts each hand a compares each card value 
                    finding the first non equal one

    Returns:        WIN, LOSE, or TIE
*/

function compare_hands_by_individual_values($dealer_hand, $player_hand)
{
    $retval = TIE;

    $dealer_ranks = get_ranks($dealer_hand);
    $player_ranks = get_ranks($player_hand);

    //Loop through each card until one is larger then the other
    sort($dealer_ranks, SORT_NUMERIC);
    sort($player_ranks, SORT_NUMERIC);

    for($i = HAND_SIZE - 1; $i > -1; $i--)
    {
        if ($dealer_ranks[$i] > $player_ranks[$i])
        {
            $retval = LOSE;
            break;
        }
        else if ($dealer_ranks[$i] < $player_ranks[$i])
        {
            $retval = WIN;
            break;
        }
    }
    return $retval;
}

/*
    Function:       parse_hand()

    Parameters:     hand        -- array of cards
                    extradesc   -- extra description
                    extra       -- extra information to pass back

    Description:    Determines type of hand

    Returns:        constant int

    Note:           Function assumes 5 card hands
*/

function parse_hand($hand, &$extradesc, &$extra)
{
    $retval = HAND_HIGH_CARD;

    //Sort the hand
    sort($hand, SORT_NUMERIC);

    $high_card = $hand[HAND_SIZE - 1];
    //Get the high card description -- used for most hands
    $high_card_desc = get_single_card_description($hand[HAND_SIZE - 1]) . " High";   


    $number_of_cards = count($hand);
    //build arrays to hold suits and ranks
    for ($i = 0; $i < $number_of_cards; $i++)
    {
        $suits[] = get_suit($hand[$i]);
        $ranks[] = get_rank($hand[$i]);
    }

    //Get the number of each kind of suit -- break the results into separate key / value pairs
    $suit_keys = array_keys(array_count_values($suits));
    $suit_vals = array_values(array_count_values($suits));
 
    //Count the number of suits
    $array_count = count($suit_keys);
    if ($array_count == 1)
    {
        $flush = true;
        $flush_type = $suit_keys[0];
    }
    else        //No flush.  Look for pairs, three of a kinds and four of a kind
    {
        //Get the number of each kind of rank -- break the results into separate key / value pairs
        $rank_keys = array_keys(array_count_values($ranks));
        $rank_vals = array_values(array_count_values($ranks));
     
        $array_count = count($rank_keys);

        for ($i = 0; $i < $array_count; $i++)
        {
            //echo("i = $i rank_key = $rank_keys[$i] rank_val = $rank_vals[$i]<BR>");
            if ($rank_vals[$i] > 1)
            {
                //inidicate that at least one card is in the hand multiple times
                $multiple = true;
                switch($rank_vals[$i])
                {
                    case 2: //pair
                        $pairs[] = $rank_keys[$i];
                        break;
                    case 3: //Three of a kind
                        $three_of_a_kind = $rank_keys[$i];
                        break;
                    case 4: //Four of a kind
                        $four_of_a_kind = $rank_keys[$i];
                        break;
                }
            }
        }
    }
    
    if (isset($multiple) == false)
    {
        /*
          Check for Straight -- Since the hand is sorted and we know that there
                                are no duplicates then the difference of the low
                                to the high card would be 4 -- for the case of 5
                                card hands
        */

        if ($hand[HAND_SIZE - 1] - $hand[0] == (HAND_SIZE - 1))
        {
            $straight = true;
        }
    }

    if (isset($straight) == true && isset($flush) == true)
    {
        $retval = HAND_STRAIGHT_FLUSH;
        $extradesc = $high_card_desc;
        $extra = $high_card;
    }
    else if (isset($four_of_a_kind) == true)
    {
        $retval = HAND_FOUR_OF_A_KIND;
        $extradesc = get_plural_rank_description($four_of_a_kind);
        $extra = $four_of_a_kind;
    }
    else if (isset($three_of_a_kind) == true && isset($pairs) == true)
    {
        $retval = HAND_FULL_HOUSE;
        $extradesc = get_plural_rank_description($three_of_a_kind) . 
                    " over " . 
                     get_plural_rank_description($pairs[0]);
        $extra = $three_of_a_kind;
    }
    else if (isset($flush))
    {
        $retval = HAND_FLUSH;
        $extradesc = $high_card_desc;
        $extra = $high_card;
    }
    else if (isset($straight))
    {
        $retval = HAND_STRAIGHT;
        $extradesc = $high_card_desc;
        $extra = $high_card;
    }
    else if (isset($three_of_a_kind) == true)
    {
        $retval = HAND_THREE_OF_A_KIND;
        $extradesc = get_plural_rank_description($three_of_a_kind);
        $extra = $three_of_a_kind;
    }
    else if (isset($pairs) && count($pairs) == 2)
    {
        //Sort the pairs
        sort($pairs, SORT_NUMERIC);
        $retval = HAND_TWO_PAIR;
        $extradesc = get_plural_rank_description($pairs[1]) . 
                    " over " . 
                     get_plural_rank_description($pairs[0]);
        $extra[] = $pairs[1];
        $extra[] = $pairs[0];
    }
    else if (isset($pairs))
    {
        $retval = HAND_PAIR;
        $extradesc = get_plural_rank_description($pairs[0]);
        $extra = $pairs[0];
    }
    else
    {
        $extradesc = $high_card_desc;       
        $extra = $high_card;
    }
    return $retval;
}

/*
    Function:       get_hand_description()

    Parameters:     hand_type -- int -- hand type
                    extra     -- extra information (high card, pair, etc)

    Description:    Returns Hand as a String

    Returns:        String
*/

function get_hand_description($hand_type, $extra)
{
    switch ($hand_type)
    {
        case HAND_STRAIGHT_FLUSH:
            $retval = $extra . DESC_STRAIGHT_FLUSH;
            break;
        case HAND_FOUR_OF_A_KIND:
            $retval = DESC_FOUR_OF_A_KIND . $extra;
            break;
        case HAND_FULL_HOUSE:
            $retval = DESC_FULL_HOUSE . $extra;
            break;
        case HAND_FLUSH:
            $retval = $extra . DESC_FLUSH;
            break;
        case HAND_STRAIGHT:
            $retval = $extra . DESC_STRAIGHT; 
            break;
        case HAND_THREE_OF_A_KIND:
            $retval = DESC_THREE_OF_A_KIND . $extra;
            break;
        case HAND_TWO_PAIR:
            $retval = DESC_TWO_PAIR . $extra;
            break;
        case HAND_PAIR:
            $retval = DESC_PAIR . $extra;
            break;
        case HAND_HIGH_CARD:
            $retval = $extra; 
            break;
    }
    return $retval;
}

/*
    Function:       get_ranks()

    Parameters:     hand -- Hand of card values (raw 0 - 51)

    Description:    retrieves and returns an array of card ranks

    Returns:        array of card ranks
*/

function get_ranks($hand)
{
    for ($i = 0; $i < HAND_SIZE; $i++)
    {
        $retval[] = get_rank($hand[$i]);
    }

    return $retval;
}

/*
    Function:       display_hand()
    
    Parameters:     $hand -- hand of cards to display

    Description:    Displays each card in a hand

    Returns:        Nothing
*/

function display_hand($hand)
{
    for ($i = 0; $i < HAND_SIZE; $i++)
    {
        $desc = get_card_description($hand[$i]);
        echo "$desc<BR>";
    }
}

?>