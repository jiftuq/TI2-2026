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

/* -----------------------------------------------------------------------------
 * 2) Connexion à la base de données via PDO
 *    - try/catch + classe Exception
 *    - jeu de caractères utf8mb4
 *    - fetch par défaut en tableau associatif
 *    - erreurs en mode Exception
 * --------------------------------------------------------------------------- */
try {
    $dsn = DB_DRIVER . ":host=" . DB_HOST
         . ";port="     . DB_PORT
         . ";dbname="   . DB_NAME
         . ";charset="  . DB_CHARSET;

    $db = new PDO($dsn, DB_LOGIN, DB_PWD, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (Exception $e) {
    
    die($e->getMessage());
}

/* -----------------------------------------------------------------------------
 * 3) Traitement du formulaire si soumission POST
 *    Variable $messageRetour utilisée par la vue :
 *      - null   : pas de soumission
 *      - true   : insertion OK   → message vert
 *      - false  : insertion KO   → message rouge
 * --------------------------------------------------------------------------- */
$messageRetour = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // On récupère les champs en s'assurant qu'ils sont des strings
    // (cast explicite + isset = champ inexistant => "")
    $firstname = isset($_POST["firstname"]) ? (string)$_POST["firstname"] : "";
    $lastname  = isset($_POST["lastname"])  ? (string)$_POST["lastname"]  : "";
    $usermail  = isset($_POST["usermail"])  ? (string)$_POST["usermail"]  : "";
    $phone     = isset($_POST["phone"])     ? (string)$_POST["phone"]     : "";
    $postcode  = isset($_POST["postcode"])  ? (string)$_POST["postcode"]  : "";
    $message   = isset($_POST["message"])   ? (string)$_POST["message"]   : "";

    // La validation/nettoyage est faite DANS le modèle (cf. consignes)
    $messageRetour = addGuestbook(
        $db,
        $firstname,
        $lastname,
        $usermail,
        $phone,
        $postcode,
        $message
    );
}

/* -----------------------------------------------------------------------------
 * 4) Récupération des messages — BONUS PAGINATION
 *    - on lit la page courante dans $_GET (ctype_digit pour ne laisser passer
 *      que des chaînes 100% numériques, pas de signe, pas de point)
 *    - on récupère le nombre total de messages
 *    - on calcule le nombre de pages
 *    - on récupère uniquement les messages de la page courante
 *    - on génère le HTML de la pagination
 * --------------------------------------------------------------------------- */
$pageActu = 1;
if (isset($_GET[PAGINATION_GET]) && ctype_digit((string)$_GET[PAGINATION_GET])) {
    $pageActu = (int)$_GET[PAGINATION_GET];
    if ($pageActu < 1) $pageActu = 1;
}

$nbTotal = getNbTotalGuestbook($db);

// On clampe $pageActu pour ne pas dépasser le nombre réel de pages
$nbPages = (int)max(1, ceil($nbTotal / PAGINATION_NB));
if ($pageActu > $nbPages) $pageActu = $nbPages;

// Messages de la page courante (vide si table vide)
$listeMessages = getGuestbookPagination($db, $pageActu, PAGINATION_NB);

// HTML de la pagination (vide si <= 1 page)
$htmlPagination = pagination($nbTotal, "./?", PAGINATION_GET, $pageActu, PAGINATION_NB);

/* -----------------------------------------------------------------------------
 * 5) Chargement de la vue
 * --------------------------------------------------------------------------- */
include URL_BASE . "/view/guestbookView.php";

/* -----------------------------------------------------------------------------
 * 6) Fermeture propre de la connexion
 * --------------------------------------------------------------------------- */
$db = null;