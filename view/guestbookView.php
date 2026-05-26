<?php

function dateFr(string $datemysql): string
{
   
    $ts = strtotime($datemysql);
    if ($ts === false) return $datemysql;
    return "Le ( " . date("d/m/Y", $ts) . " à " . date("H\\hi", $ts) . " )";
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TI2 | Livre d'or</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="topbar">
    <div class="topbar-inner">
        <div class="logo" aria-hidden="true">CF2m</div>
        <div class="topbar-title">
            <h1>Livre d'or</h1>
            <p class="subtitle">Laissez une trace de votre passage&nbsp;!</p>
        </div>
        <button id="toggle-theme" type="button" aria-label="Changer de thème">
            🌙 Dark Mode
        </button>
    </div>
</header>

<main class="container">

    
    <section class="card form-card">

        <img src="img/favicon.png" alt="Illustration livre d'or" class="illustration">

        <div class="form-wrapper">
            <h2>Laissez votre message</h2>

            
            <div id="messages" class="messages" role="alert" aria-live="polite">
                <?php
                
                if ($messageRetour === true) {
                    echo '<p class="msg-success">Merci pour votre nouveau message</p>';
                } elseif ($messageRetour === false) {
                    echo '<p class="msg-error">Problème lors de l\'envoi du message</p>';
                }
                ?>
            </div>

            
            <form id="guestbook-form" action="" method="POST" novalidate>

                <div class="field">
                    <label for="firstname">Prénom</label>
                    <input type="text" id="firstname" name="firstname"
                           maxlength="100" placeholder="Ex : John" required>
                </div>

                <div class="field">
                    <label for="lastname">Nom</label>
                    <input type="text" id="lastname" name="lastname"
                           maxlength="100" placeholder="Ex : Smith" required>
                </div>

                <div class="field">
                    <label for="usermail">E-mail</label>
                    <input type="email" id="usermail" name="usermail"
                           maxlength="200" placeholder="john.smith@example.com" required>
                </div>

                <div class="field">
                    <label for="postcode">Code postal</label>
                    <input type="text" id="postcode" name="postcode"
                           maxlength="4" placeholder="1000" inputmode="numeric" required>
                </div>

                <div class="field">
                    <label for="phone">Téléphone</label>
                    <input type="tel" id="phone" name="phone"
                           maxlength="20" placeholder="0498 15 08 82" required>
                </div>

                <div class="field">
                    <label for="message">Message</label>
                    <textarea id="message" name="message"
                              maxlength="300" rows="4"
                              placeholder="Un petit mot..." required></textarea>
                    <small id="char-counter" class="counter">0 / 300 caractères</small>
                </div>
                <div class="form-group form-group-checkbox">
                        <label class="checkbox-label">
                            <input type="checkbox" name="rgpd" id="rgpd" required aria-required="true">
                            <span class="checkmark"></span>
                            <span class="checkbox-text">J'accepte le stockage de mes données personnelles.</span>
                        </label>
                    </div>

                <button type="submit" class="btn-submit btn-mono">
                    Envoyer le message
                </button>
            </form>
        </div>
    </section>

   
    <section class="messages-section">

        <h2>Les messages précédents</h2>

        <p class="count-info">
            <?php
            if ($nbTotal === 0) {
                echo "pas encore de message";
            } elseif ($nbTotal === 1) {
                echo "Il y a 1 message";
            } else {
                echo "Il y a " . $nbTotal . " messages";
            }
            ?>
        </p>

        <?php
        
        if ($htmlPagination !== "") echo $htmlPagination;
        ?>

        <?php if (!empty($listeMessages)) : ?>
            <ul class="post-list">
                <?php foreach ($listeMessages as $post) : ?>
                    <li class="post-card post card">
                        <div class="post-head">
                            <strong class="post-name">
                                <?= $post["firstname"] ?> <?= $post["lastname"] ?>
                            </strong>
                            <span class="post-mail"><?= $post["usermail"] ?></span>
                            <em class="post-date">
                                <?= dateFr($post["datemessage"]) ?>
                            </em>
                        </div>
                        
                        <p class="post-msg"><?= $post["message"] ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php
            
            if ($htmlPagination !== "") echo $htmlPagination;
            ?>
        <?php endif; ?>

    </section>

</main>


<script src="js/jquery.min.js"></script>
<script src="js/validation.js"></script>
</body>
</html>


