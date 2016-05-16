<?
session_register("initialized");
session_register("points");
session_register("deck");

    require("consts.php");
    require("hands.php");
    require("cards.php");

    echo(HTML_START);
    echo(BODY_START);
    echo(HEAD_START);
    echo(HEAD_END);

    /*
        Only bring up the main screen if the session variables had been 
        initialzed from index.php
    */
    if ($initialized == true)
    {
        echo("<FORM ACTION='poker.php' METHOD=POST>");
        echo("<INPUT TYPE=TEXT NAME='bet'>");
        echo("<INPUT TYPE=SUBMIT NAME='dobet' VALUE='Place Your Bet'>");
        echo("</FORM>");

        main($deck, $bet, $dobet, $points);
    }
    else
    {
        echo("Unauthorized access<BR>");
    }

    echo(BODY_END);
    echo(HTML_END);

/*
    Function:       main()
    
    Parameters:     deck        -- deck of card array
                    bet         -- wager amount 
                    dobet       -- variable set from submit
                    points   -- currency


    Description:    Sets up main game screen and controls game.
                    Checks for valid bet.

    Returns:        Nothing

    Modifies:       deck, points
*/

function main(&$deck, $bet, $dobet, &$points)
{

    echo("<BR>You have $points points to bet<BR>");
    if (isset($dobet))
    {
        //Check bet
        if ($bet <= 0)
        {
            echo("<BR>You must bet at least 1 neopoint<BR>");
        }
        else if ($bet > $points)
        {
            echo("<BR>You bet more points then you currently possess<BR>");
        }
        else
        {
            //Play game
            play_game($deck, $points, $bet);
        }    
    }
    else
    {
        echo("<BR>Place your bets<BR>");
    }

}

/*
    Function:       play_game()
    
    Parameters:     deck        -- deck of card array
                    points   -- currency
                    bet         -- wager amount 


    Description:    Deals hands, compares the hands, handles bet

    Returns:        Nothing

    Modifies:       deck, points
*/

function play_game(&$deck, &$points, $bet)
{
    //Shuffle the deck of cards
    shuffle_deck($deck);

    //Deal the first five cards to the player
    $playerhand = array_slice ($deck, 0, HAND_SIZE);   
    echo ("Player's hand<BR>");
    //Display each card as text
    display_hand($playerhand);
    //Parse the hand
    $player_hand_type = parse_hand($playerhand, &$player_extra_description, &$player_extra_info);
    //Get the hand description as text
    $player_hand_description = get_hand_description($player_hand_type, $player_extra_description);
    echo("<BR>PLAYER HAND = $player_hand_description<BR>");
    echo("<BR>");

    //Deal the next five cards to the dealer
    $dealerhand = array_slice ($deck, HAND_SIZE, HAND_SIZE);
    echo ("Dealer's hand<BR>");
    //Display each card as text
    display_hand($dealerhand);

    echo("<BR>");
    //Parse the hand
    $dealer_hand_type = parse_hand($dealerhand, &$dealer_extra_description, &$dealer_extra_info);
    //Get the hand description as text
    $dealer_hand_description = get_hand_description($dealer_hand_type, $dealer_extra_description);
    echo("DEALER HAND = $dealer_hand_description<BR>");

    //determine the outcome
    $outcome = compare_hands($dealer_hand_type, 
                             $dealerhand, 
                             $dealer_extra_info, 
                             $player_hand_type, 
                             $playerhand, 
                             $player_extra_info);

    if ($outcome == LOSE)
    {
        echo("You lost<BR>");
        $points -= $bet;
    }
    else if ($outcome == WIN)
    {
        echo("You won<BR>");
        $points += $bet;
    }
    else
    {
        echo("You tied<BR>");
    }

    echo("Your now have $points points<BR>");

    if ($points == 0)
    {
        echo("<BR>Game over<BR>");
    }
}


/*
    Function:       shuffle_deck()

    Parameters:     originaldeck [By reference]

    Description:    Randomizes the contents of an array

    Returns:        Nothing

    Note:           Needed to create this function due to a bug in the shuffle function
*/

function shuffle_deck(&$theDeck)
{
    $numberofCards = count($theDeck);

    for ($currentElement = 0; $currentElement < $numberofCards; $currentElement++)
    {
        //Store the value of the current element
        $thisElementValue = $theDeck[$currentElement];
        //Pick a random index from the array
        $randomIndex = rand(0, $numberofCards - 1);         
        //Swap the element at randomIndex with the current element
        $theDeck[$currentElement] = $theDeck[$randomIndex];
        $theDeck[$randomIndex] = $thisElementValue;
    }    
}
?>