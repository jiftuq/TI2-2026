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


/*
 * Connexion à la base de données via PDO (avec try/catch)
 */
try {
    $db = new PDO(
        dsn: DB_DRIVER . ":host=" . DB_HOST
           . ";port="     . DB_PORT
           . ";dbname="   . DB_NAME
           . ";charset="  . DB_CHARSET,
        username: DB_LOGIN,
        password: DB_PWD,
        // tableau de paramètres de connexion, ici pour recevoir les
        // résultats des query en tableau associatif
        options: [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // modification de la connexion en dehors de celle-ci
    // (gestion d'erreurs en mode Exception, valeur par défaut de PDO depuis PHP 8.0)
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // force les vraies requêtes préparées natives MySQL (au lieu de l'émulation côté PHP)
    // → protection renforcée contre les injections SQL
    // → permet à PDO::PARAM_INT de fonctionner pour LIMIT/OFFSET
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (Exception $e) {
    // arrêt et affichage de l'erreur (en dev)
    die($e->getMessage());
}


/*
 * Si le formulaire a été soumis
 */
if (isset(
    $_POST["firstname"],
    $_POST["lastname"],
    $_POST["usermail"],
    $_POST["phone"],
    $_POST["postcode"],
    $_POST["message"]
)) {

    // tentative d'insertion (protections dans la fonction)
    $insertOk = addGuestbook(
        db:        $db,
        firstname: $_POST["firstname"],
        lastname:  $_POST["lastname"],
        usermail:  $_POST["usermail"],
        phone:     $_POST["phone"],
        postcode:  $_POST["postcode"],
        message:   $_POST["message"]
    );

    // si l'insertion a réussi → redirection (pattern PRG, évite le double-submit sur F5)
    if ($insertOk) {
        header("Location: index.php?inserted=1");
        exit;
    }
    // sinon : on continue le script, la vue réaffichera la page avec les $_POST
    // encore disponibles pour repeupler les champs
}


/*
 * Pagination (BONUS)
 */

// on vérifie sur quelle page on est (string contenant que des numériques sans "." ni "-" => ctype_digit)
$pageActu = 1;
if (isset($_GET[PAGINATION_GET]) && ctype_digit((string)$_GET[PAGINATION_GET])) {
    $pageActu = (int)$_GET[PAGINATION_GET];
    if ($pageActu < 1) $pageActu = 1;
}

// on compte le nombre total de messages (SQL)
$nbTotal = getNbTotalGuestbook($db);

// clamp si l'utilisateur entre une page qui n'existe pas (ex: ?pg=999)
$nbPages = (int)max(1, ceil($nbTotal / PAGINATION_NB));
if ($pageActu > $nbPages) $pageActu = $nbPages;

// on récupère le HTML de la pagination
$htmlPagination = pagination($nbTotal, "./?", PAGINATION_GET, $pageActu, PAGINATION_NB);

// on récupère les messages de la page courante
$listeMessages = getGuestbookPagination($db, $pageActu, PAGINATION_NB);


// Appel de la vue
include URL_BASE . "/view/guestbookView.php";


// fermeture de la connexion (bonne pratique)
$db = null;