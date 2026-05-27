<?php
# public/index.php


/*
 * Front Controller de la gestion du livre d'or
 */

/*
 * Chargement des dépendances
 */
// chargement de configuration
require_once "../config.php";
// chargement du modèle de la table guestbook
require_once URL_BASE . "/model/guestbookModel.php";



try {
    $db = new PDO(
        dsn: DB_DRIVER . ":host=" . DB_HOST
           . ";port="     . DB_PORT
           . ";dbname="   . DB_NAME
           . ";charset="  . DB_CHARSET,
        username: DB_LOGIN,
        password: DB_PWD,
        
        options: [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (Exception $e) {
    
    die($e->getMessage());
}


$insertOk = null;


if (isset($_GET['inserted'])) {
    $insertOk = true;
}

if (isset(
    $_POST["firstname"],
    $_POST["lastname"],
    $_POST["usermail"],
    $_POST["phone"],
    $_POST["postcode"],
    $_POST["message"]
)) {

    
    $insertOk = addGuestbook(
        db:        $db,
        firstname: $_POST["firstname"],
        lastname:  $_POST["lastname"],
        usermail:  $_POST["usermail"],
        phone:     $_POST["phone"],
        postcode:  $_POST["postcode"],
        message:   $_POST["message"]
    );

    
    if ($insertOk) {
        header("Location: index.php?inserted=1");
        exit;
    }
    
}




$pageActu = 1;
if (isset($_GET[PAGINATION_GET]) && ctype_digit((string)$_GET[PAGINATION_GET])) {
    $pageActu = (int)$_GET[PAGINATION_GET];
    if ($pageActu < 1) $pageActu = 1;
}


$nbTotal = getNbTotalGuestbook($db);


$nbPages = (int)max(1, ceil($nbTotal / PAGINATION_NB));
if ($pageActu > $nbPages) $pageActu = $nbPages;

$htmlPagination = pagination($nbTotal, "./?", PAGINATION_GET, $pageActu, PAGINATION_NB);


$listeMessages = getGuestbookPagination($db, $pageActu, PAGINATION_NB);



include URL_BASE . "/view/guestbookView.php";



$db = null;