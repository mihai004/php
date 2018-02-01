<?php
require('Models/UserDataSet.php');
require('Models/BookDataSet.php');
require('Models/BasketDataSet.php');


$view = new stdClass();
$view->pageTitle = 'Books';

$booksDataSet = new BooksDataSet();
$basketDataSet = new BasketDataSet();
$userDataSet = new UserDataSet();

$total = $booksDataSet->countItems();
$limit = 0;
$start = 0;

if(isset($_GET['limit'])) {

    $limit = $_GET['limit'];

}
else {

    $limit = 10;

}

if(isset($_GET['page'])) {

    $page = $_GET['page'];
    $start = ($page > 1) ? ($page * $limit) - $limit: 0;

}


if (empty($view->booksDataSet)) {

    $view->booksDataSet = $booksDataSet->fetchAllBooks($start, $limit);

}

if(isset($_POST['look'])) {

    $view->booksDataSet = $booksDataSet->searchByAttribute($_POST['search']);

}

elseif(isset($_GET['sort'])) {

    $total = $booksDataSet->countFilters($_GET['filter_1'], $_GET['filter_2']);
    $view->booksDataSet = $booksDataSet->searchFor($_GET['filter_1'], $_GET['filter_2'], $start, $limit);

}


if(isset($_SESSION['userID'])) {

    $view->user = $userDataSet->searchUser('idUser', $_SESSION['userID']);

}

require('Views/shopList.phtml');
