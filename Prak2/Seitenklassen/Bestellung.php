<?php

declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
/**
 * Class PageTemplate for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 7.4
 *
 * @file     PageTemplate.php
 * @package  Page Templates
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 * @version  3.1
 */

// to do: change name 'PageTemplate' throughout this file
require_once './Page.php';

/**
 * This is a template for top level classes, which represent
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class.
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking
 * during implementation.
 * @author   Bernhard Kreling, <bernhard.kreling@h-da.de>
 * @author   Ralf Hahn, <ralf.hahn@h-da.de>
 */
class Bestellung extends Page
{
    // to do: declare reference variables for members 
    // representing substructures/blocks

    /**
     * Instantiates members (to be defined above).
     * Calls the constructor of the parent i.e. page class.
     * So, the database connection is established.
     * @throws Exception
     */
    protected function __construct()
    {
        parent::__construct();
        // to do: instantiate members representing substructures/blocks
    }

    /**
     * Cleans up whatever is needed.
     * Calls the destructor of the parent i.e. page class.
     * So, the database connection is closed.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Fetch all data that is necessary for later output.
     * Data is returned in an array e.g. as associative array.
     * @return array An array containing the requested data. 
     * This may be a normal array, an empty array or an associative array.
     */
    protected function getViewData(): array
    {
        $sql = "SELECT * FROM `article`";
        $recordset = $this->_database->query($sql);
        
        if (!$recordset) {
            throw new Exception("Charset failed: ".$this->_database->error);
        }

        $data = $recordset->fetch_all();
        $recordset->free();
        
        return $data;
    }

    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
     * @return void
     */
    
    protected function generateView(): void
    {
        $data = $this->getViewData();
        $this->generatePageHeader("Bestellung" , "", false); //to do: set optional parameters
        // to do: output view of this page
        echo <<<HTML
        <h2>Bestellung</h2>

        <section class="speisekarte">
        <h3>Speisekarte</h3>

        HTML;
        foreach($data as $pizza) {
        $pizzaName = $pizza[1];
        $price = $pizza[3];
        
        echo <<<PIZZAS
            <img src="..$pizza[2]" alt=$pizza[1] width="400" height="200"><br>
            <p>{$pizzaName} : {$price}€</p> 
        PIZZAS;

        }
        echo <<<HTML
        </section>

        <section class="warenkorb">
        <h3>Warenkorb</h3>
        <form action="./Bestellung.php" method="POST">
            <select name="pizza[]" id="pizza" tabindex="1"  size=3 multiple>
                <option value="Margherita">Margherita</option>
                <option value="Salami">Salami</option>
                <option value="Hawaii">Hawaii</option>
            </select>
            <p>14,50€</p>
            <input type="text" name="address" id="address" value="" placeholder="Ihre Adresse ..."><br><br>
            <button tabindex="2" accesskey="a">Alle Löschen</button>
            <button tabindex="3" accesskey="s">Auswahl Löschen</button>
            <button type="submit" tabindex="4" accesskey="b">Bestellen</button>
        </form>
        </section>
        HTML;

        $this->generatePageFooter();
    }

    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
     * @return void
     */
    protected function processReceivedData(): void
    {
        parent::processReceivedData();
        // to do: call processReceivedData() for all members
        
        if (isset($_POST["pizza"]) && isset($_POST["address"])) {
            // insert new ordering
            $sqlOrdering = "INSERT INTO ordering (address, ordering_time)
                    VALUES (\"" . $_POST["address"] . "\",\"" . date('Y-m-d H:i:s') . "\");";
            
            if ($this->_database->query($sqlOrdering) === FALSE) 
                echo "Error: " . $sqlOrdering . "<br>" . $this->_database->error;
            $orderingId = mysqli_fetch_array($this->_database->query("SELECT LAST_INSERT_ID();"))[0];

            //insert all articles
            foreach ($_POST["pizza"] as $key) {
                $sqlArticleId = "SELECT article_id FROM article WHERE name = \"" . $key . "\";";
                $articleId = mysqli_fetch_array($this->_database->query($sqlArticleId))[0];
                $InsertArticle = "INSERT INTO ordered_article (ordering_id, article_id,status)
                            VALUES (" . $orderingId . "," . $articleId . ",0);";
                if ($this->_database->query($InsertArticle) === FALSE)
                    echo "Error: " . $sqlOrdering . "<br>" . $this->_database->error;

            }
            header('Location: ./kunde.php');
            die;
        }
    }

    /**
     * This main-function has the only purpose to create an instance
     * of the class and to get all the things going.
     * I.e. the operations of the class are called to produce
     * the output of the HTML-file.
     * The name "main" is no keyword for php. It is just used to
     * indicate that function as the central starting point.
     * To make it simpler this is a static function. That is you can simply
     * call it without first creating an instance of the class.
     * @return void
     */
    public static function main(): void
    {
        try {
            $page = new Bestellung();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            //header("Content-type: text/plain; charset=UTF-8");
            header("Content-type: text/html; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

// This call is starting the creation of the page. 
// That is input is processed and output is created.
//Aufruf der statischen methode main der klasse bestellung
Bestellung::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >