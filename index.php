<?
session_start();
require("consts.php");
main($deck, $points, $initialized);

/*
    Function:       main()
    
    Parameters:     deck        -- deck of card array
                    points   -- currency
                    initialized -- initialized flag

    Description:    Sets up main screen, initializes session variables

    Returns:        Nothing

    Modifies:       deck, points, initialized
*/

function main(&$deck, &$points, &$initialized)
{
    echo(BODY_START);
    echo(HEAD_START);
    echo(HEAD_END);
    echo("<CENTER>");

    init($deck, $points, $initialized);
    
    echo("Welcome to poker<BR>");
    echo("<FORM ACTION='poker.php' METHOD=POST>");
    echo("<INPUT TYPE=SUBMIT NAME='BEGIN' VALUE='BEGIN'>");
    echo("</FORM>");

    echo(BODY_END);
    echo(HTML_END);
}


/*
    Function:       init()

    Parameters:     deck            -- array of cards
                    points       -- bankroll
                    initialized     -- flag indicating system has been initialized

    Description:    Initiailizes the game elements

    Returns:        Nothing

    Modifies:       deck, points, initialized
*/



function init(&$deck, &$points, &$initialized)
{
    $_SESSION['points'] = STARTING_POINTS;
    $_SESSION['initialized'] = true;
    /*
      Seed random generated number -- Notes: 1) documentation is incorrect do 
                                                not use float
                                             2) Make sure this is only called 
                                                once per running of my script 
    */
    srand((double)microtime() * RANDOM_SEED);
    $_SESSION['deck'] = range(0, DECK_SIZE);               //Initialize the deck
}

?>