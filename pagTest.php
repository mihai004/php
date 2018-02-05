<?php
require('Models/UserDataSet.php');
require('Models/BookDataSet.php');
require('Models/BasketDataSet.php');


$view = new stdClass();
$view->pageTitle = 'Books';

$booksDataSet = new BooksDataSet();
if(isset($_GET['limit'])) {

    $limit = $_GET['limit'];
    echo $limit;

}

if (empty($view->booksDataSet)) {

    $view->booksDataSet = $booksDataSet->fetchAllBooks(1, $limit);

}



require ('Views/pagFile.phtml');