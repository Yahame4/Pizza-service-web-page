<?php declare(strict_types=1);
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
class Kunde extends Page
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
    protected function getViewData():array
    {
        //Session verwaltung - Zugriff auf persistente Variable
        if (!isset($_SESSION['orderid'])) {
            $_SESSION['orderid'] = NULL;
        }

        $orderingId = $_SESSION["orderid"];

        // SQL-Abfrage festlegen
        $sql = "SELECT `article`.`name`, `ordered_article`.`status`, `ordered_article`.`ordered_article_id`
                FROM `ordered_article`
                LEFT JOIN `article`
                ON `article`.`article_id` = `ordered_article`.`article_id`
                WHERE `ordered_article`.`ordering_id` = '{$orderingId}'";
        //Save ur life
        $recordset = $this->_database->query($sql); //Ergebnistabelle abfragen
        
        if (!$recordset) {
            throw new Exception("Error: ".$this->_database->error);
        }

        $pizzalist = $recordset->fetch_all(); //liefert alle Zielen als Array von assoziativen Arrays
        $recordset->free(); //Speicher der Ergebnistabelle freigeben
        return $pizzalist;
    }

    /**
     * First the required data is fetched and then the HTML is
     * assembled for output. i.e. the header is generated, the content
     * of the page ("view") is inserted and -if available- the content of
     * all views contained is generated.
     * Finally, the footer is added.
	 * @return void
     */
    protected function generateView():void
    {
        $pizzalist = $this->getViewData(); // NOSONAR ignore unused $data
        $this->generatePageHeader('Kunde');
        
        echo <<<HTML
            <script src="./../js/PizzaStatus.js" onload='requestData()'></script>
            <div class="container">
                <p id="customerDiv">
                </p>
            </div>
        HTML;

        $this->generatePageFooter();
    }

    protected function getStatus($status): String
    {
        if($status == 0) {return "bestellt";}
        else if($status == 1) {return "im Ofen";}
        else if($status == 2) {return "fertig";}
        else if($status == 3) {return "unterwegs";}
        else if($status == 4) {return "geliefert";}
        else return "default";
    }


    /**
     * Processes the data that comes via GET or POST.
     * If this page is supposed to do something with submitted
     * data do it here.
	 * @return void
     */
    protected function processReceivedData():void
    {
        parent::processReceivedData();
        //headers for AJAX 
        header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Sat, 01 Jul 2000 06:00:00 GMT"); // Datum in der Vergangenheit
        header("Cache-Control: post-check=0, pre-check=0", false); // fuer IE
        header("Pragma: no-cache");
        session_cache_limiter('nocache'); // VOR session_start()!
        session_cache_expire(0);

        session_start(); // Session eröffnen (Login) bzw. restaurieren (Folgeseiten)
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
    public static function main():void
    {
        try {
            $page = new Kunde();
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
Kunde::main();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >