<?php
# model/guestbookModel.php



/**
 * @param PDO    $db
 * @param string $firstname    <= 100
 * @param string $lastname     <= 100
 * @param string $usermail     <= 200, doit être un email valide
 * @param string $phone        <= 20, uniquement numérique
 * @param string $postcode     == 4, uniquement numérique
 * @param string $message      <= 500
 * @return bool true si l'insertion a réussi, false sinon
 
 */
function addGuestbook(PDO $db,
                    string $firstname,
                    string $lastname,
                    string $usermail,
                    string $phone,
                    string $postcode,
                    string $message
): bool
{
    
    $firstname = trim($firstname);
    $lastname  = trim($lastname);
    $usermail  = trim($usermail);
    $phone     = trim($phone);
    $postcode  = trim($postcode);
    $message   = trim($message);

   

    $firstname = htmlspecialchars($firstname, );
    $lastname  = htmlspecialchars($lastname,  );
    $usermail  = htmlspecialchars($usermail,  );
    $phone     = htmlspecialchars($phone,     );
    $postcode  = htmlspecialchars($postcode,  );
    $message   = htmlspecialchars($message,   );

   
    if (mb_strlen($firstname) > 100) return false;
    if (mb_strlen($lastname)  > 100) return false;
    if (mb_strlen($usermail)  > 200) return false;
    if (mb_strlen($phone)     >  20) return false;
    if (mb_strlen($postcode)  !==  4) return false;
    if (mb_strlen($message)   > 500) return false;

   
    try {
        $sql = "INSERT INTO `guestbook`
                    (`firstname`, `lastname`, `usermail`, `phone`, `postcode`, `message`)
                VALUES
                    (:firstname, :lastname, :usermail, :phone, :postcode, :message)";

        $stmt = $db->prepare($sql);

        $stmt->bindValue(":firstname", $firstname, PDO::PARAM_STR);
        $stmt->bindValue(":lastname",  $lastname,  PDO::PARAM_STR);
        $stmt->bindValue(":usermail",  $usermail,  PDO::PARAM_STR);
        $stmt->bindValue(":phone",     $phone,     PDO::PARAM_STR);
        $stmt->bindValue(":postcode",  $postcode,  PDO::PARAM_STR);
        $stmt->bindValue(":message",   $message,   PDO::PARAM_STR);

        $stmt->execute();

        
        $ok = ($stmt->rowCount() === 1);
        $stmt->closeCursor();

        return $ok;

    } catch (PDOException $e) {
        
        die($e->getMessage());
    }
}


/**
 * @param PDO $db
 * @return array tableau des messages du plus récent au plus ancien, ou tableau vide
 */
function getAllGuestbook(PDO $db): array
{
    try {
        
        $sql  = "SELECT `id`, `firstname`, `lastname`, `usermail`, `message`, `datemessage`
                 FROM `guestbook`
                 ORDER BY `datemessage` DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $messages = $stmt->fetchAll(); 

        
        $stmt->closeCursor();

        
        return $messages;

    } catch (PDOException $e) {
        die($e->getMessage());
    }
}


/**
 * @param PDO $db
 * @return int nombre total de messages dans la table
 */
function getNbTotalGuestbook(PDO $db): int
{
    try {
        $sql  = "SELECT COUNT(*) AS `nb` FROM `guestbook`";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $row = $stmt->fetch();
        $stmt->closeCursor();

        return (int)($row["nb"] ?? 0);

    } catch (PDOException $e) {
        die("Erreur SQL (COUNT) : " . $e->getMessage());
    }
}


/**
 * @param PDO $db
 * @param int $pageActu page courante (1 par défaut)
 * @param int $limit    nombre de messages par page
 * @return array messages de la page courante (vide si aucun)
 */
function getGuestbookPagination(PDO $db, int $pageActu = 1, int $limit = 5): array
{
    
    if ($pageActu < 1) $pageActu = 1;
    if ($limit    < 1) $limit    = 5;

    $offset = ($pageActu - 1) * $limit;

    try {
        $sql  = "SELECT `id`, `firstname`, `lastname`, `usermail`, `message`, `datemessage`
                 FROM `guestbook`
                 ORDER BY `datemessage` DESC
                 LIMIT :lim OFFSET :off";
        $stmt = $db->prepare($sql);

        
        $stmt->bindValue(":lim", $limit,  PDO::PARAM_INT);
        $stmt->bindValue(":off", $offset, PDO::PARAM_INT);

        $stmt->execute();
        $messages = $stmt->fetchAll();
        $stmt->closeCursor();

        return $messages;

    } catch (PDOException $e) {
        die($e->getMessage());
    }
}


/**
 * @param int    $nbtotalMessage  nombre total de messages
 * @param string $url             base de l'URL (ex: "./?")
 * @param string $get             nom de la variable GET (ex: "pg")
 * @param int    $pageActu        page courante
 * @param int    $perPage         messages par page
 * @return string HTML de la pagination, "" si <= 1 page
 */
function pagination(int $nbtotalMessage, string $url = "./?", string $get = "page", int $pageActu = 1, int $perPage = 5): string
{
    $sortie = "";
    if ($nbtotalMessage === 0) return "";
    $nbPages = (int)ceil($nbtotalMessage / $perPage);
    if ($nbPages == 1) return "";

    $sortie .= "<p class='pagination'>";
    for ($i = 1; $i <= $nbPages; $i++) {
        if ($i === 1) {
            if ($pageActu === 1) {
                $sortie .= "<< < 1 |";
            } elseif ($pageActu === 2) {
                $sortie .= " <a href='$url'><<</a> <a href='$url'><</a> <a href='$url'>1</a> |";
            } else {
                $sortie .= " <a href='$url'><<</a> <a href='$url&$get=" . ($pageActu - 1) . "'><</a> <a href='$url'>1</a> |";
            }
        } elseif ($i < $nbPages) {
            if ($i === $pageActu) {
                $sortie .= "  $i |";
            } else {
                $sortie .= "  <a href='$url&$get=$i'>$i</a> |";
            }
        } else {
            if ($pageActu >= $nbPages) {
                $sortie .= "  $nbPages > >>";
            } else {
                $sortie .= "  <a href='$url&$get=$nbPages'>$nbPages</a> <a href='$url&$get=" . ($pageActu + 1) . "'>></a> <a href='$url&$get=$nbPages'>>></a>";
            }
        }
    }
    $sortie .= "</p>";
    return $sortie;
}
