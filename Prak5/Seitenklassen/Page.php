<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
/**
 * Class Page for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     Page.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @version  3.1
 */

/**
 * This abstract class is a common base class for all
 * HTML-pages to be created.
 * It manages access to the database and provides operations
 * for outputting header and footer of a page.
 * Specific pages have to inherit from that class.
 * Each derived class can use these operations for accessing the database
 * and for creating the generic parts of a HTML-page.
 *
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 */
abstract class Page
{
    // --- ATTRIBUTES ---

    /**
     * Reference to the MySQLi-Database that can be used
     * by all operations of the class or inherited classes.
     */
    protected MySQLi $_database;

    // --- OPERATIONS ---

    /**
     * Connects to DB and stores
     * the connection in member $_database.
     * Needs name of DB, user, password.
     */
    protected function __construct()
    {
        error_reporting(E_ALL);

        $host = "localhost";
        $username = "public";
        $password = "public";
        $dbname = "pizzaservice";
        /********************************************/
        // This code switches from the the local installation (XAMPP) to the docker installation 
        if (gethostbyname('mariadb') != "mariadb") { // mariadb is known?
            $host = "mariadb";
        }
        /********************************************/

        $this->_database = new MySQLi($host, $username, $password, $dbname); //TCP/ IP-Verbindung aufbauen

        //Verbindung prüfen
        if (mysqli_connect_errno()) {
            throw new Exception("Connect failed: " . mysqli_connect_error());
        }

        if (!$this->_database->set_charset("utf8")) {
            throw new Exception($this->_database->error);
        }
    }

    /**
     * Closes the DB connection and cleans up
     */
    public function __destruct()
    {
        $this->_database->close(); // TCP/IP-Verbindung schliessen
    }

    /**
     * Generates the header section of the page.
     * i.e. starting from the content type up to the body-tag.
     * Takes care that all strings passed from outside
     * are converted to safe HTML by htmlspecialchars.
     *
     * @param string $title $title is the text to be used as title of the page
     * @param string $jsFile path to a java script file to be included, default is "" i.e. no java script file
     * @param bool $autoreload  true: auto reload the page every 5 s, false: not auto reload
     * @return void
     */
    protected function generatePageHeader(string $title = "", string $jsFile = "", bool $autoreload = false): void
    {
        $title = htmlspecialchars($title);
        header("Content-type: text/html; charset=UTF-8");

        echo <<<HTML
        <!DOCTYPE html>
        <html lang="de">
        <head>
            <meta charset="UTF-8">
            <title>$title</title>
            <script src="./../js/ECMAScript.js"></script>
            <link rel="stylesheet" href="./../CSS/style.css">
            <link rel="stylesheet" href="./../CSS/style_Baecker.css">
            <link rel="stylesheet" href="./../CSS/style_Fahrer.css">
            <link rel="stylesheet" href="./../CSS/style_Kunde.css">
        HTML;

        if ($autoreload == true) echo "<meta http-equiv=\"refresh\" content=\"10\">";

        echo <<<HTML
        </head>
        <body>

        <header>
            <h1><b>Pizzaservice</b></h1>
            <ul>
                <li><a class="active" href="/Praktikum/Prak5/Seitenklassen/Bestellung.php">Bestellung</a></li>
                <li><a href="/Praktikum/Prak5/Seitenklassen/Baecker.php">Bäcker</a></li>
                <li><a href="/Praktikum/Prak5/Seitenklassen/Fahrer.php">Fahrer</a></li>
                <li><a href="/Praktikum/Prak5/Seitenklassen/Kunde.php">Kunde</a></li>
            </ul>
        </header>
        <br>
        HTML;

        // to do: handle all parameters
        // to do: output common beginning of HTML code
    }

    /**
     * Outputs the end of the HTML-file i.e. </body> etc.
     * @return void
     */
    protected function generatePageFooter(): void
    {
        echo <<<HTML
            </body>
            </html>
        HTML;

        // to do: output common end of HTML code
    }

    /**
     * Processes the data that comes in via GET or POST.
     * If every derived page is supposed to do something common
     * with submitted data do it here. 
     * E.g. checking the settings of PHP that
     * influence passing the parameters (e.g. magic_quotes).
     * @return void
     */
    protected function processReceivedData(): void
    {
    }
} // end of class

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >