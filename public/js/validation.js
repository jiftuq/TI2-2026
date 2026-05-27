/* ============================================================================
   TRAVAIL D'INTÉGRATION JAVASCRIPT / jQuery
   Gestion d'un formulaire de contact + Dark Mode
   ============================================================================

   OBJECTIF GÉNÉRAL
   ----------------
   Créer une page contenant un formulaire de contact validé côté client en
   jQuery, avec un système de bascule entre mode clair et mode sombre.
   L'envoi final est géré par PHP qui affiche un message de retour.

   ============================================================================
   PARTIE 1 — STRUCTURE HTML À PRÉVOIR
   ============================================================================

   Vous devez créer un formulaire contenant AU MINIMUM les champs suivants :

     - Nom               (input text)
     - Prénom            (input text)
     - Email             (input email)
     - Code postal belge (input text)
     - Numéro de téléphone belge (input text)
     - Message           (textarea)
     - Bouton d'envoi    (button submit)

   Prévoir également :
     - Une zone <div id="messages"></div> en HAUT du formulaire pour afficher
       les messages d'erreur (rouge) ou de succès (vert).
     - Un bouton <button id="toggle-theme"></button> pour basculer le thème.

   ============================================================================
   PARTIE 2 — VALIDATION JAVASCRIPT (jQuery OBLIGATOIRE)
   ============================================================================

   Au clic sur le bouton d'envoi, vérifier CHAQUE champ.
   Si un champ ne respecte pas sa condition, afficher un message EN ROUGE
   en haut du formulaire, dans la zone #messages.
   Si TOUS les champs sont valides, afficher un message EN VERT et envoyer
   le formulaire (qui sera traité par PHP — voir partie 3).

   --- RÈGLES DE VALIDATION ---

   1) Nom et Prénom
      - Champs obligatoires (non vides)
      - Au moins 2 caractères

   2) Email
      - Champ obligatoire
      - Doit respecter le format d'une adresse email valide
        (utiliser une expression régulière — regex)

   3) Code postal belge
      - 4 chiffres exactement
      - Compris entre 1000 et 9999

   4) Numéro de téléphone belge
      - Doit accepter les formats suivants :
          • 0470123456
          • 0470 12 34 56
          • +32 470 12 34 56
          • 0032470123456
      - Indice : nettoyer la chaîne (enlever espaces, tirets, points)
        AVANT de tester avec une regex

   5) Message
      - Champ obligatoire
      - Au moins 10 caractères

   --- AFFICHAGE DES MESSAGES ---

   - Tous les messages d'erreur s'affichent dans la zone #messages,
     en haut du formulaire.
   - Couleur rouge pour les erreurs, couleur verte pour le succès.
   - Vider la zone à chaque nouvelle tentative d'envoi.

   ============================================================================
   PARTIE 3 — TRAITEMENT CÔTÉ PHP
   ============================================================================

   Si tous les champs sont valides, le formulaire est envoyé à un script PHP.
   Ce script doit afficher :

     - "Merci pour votre nouveau message" en VERT si l'envoi a réussi.
     - "Problème lors de l'envoi du message" en ROUGE si l'envoi a échoué.

   Note : pour cet exercice, le PHP peut simuler la réussite/échec
   (par exemple, vérifier que les variables $_POST sont bien remplies).

   ============================================================================
   PARTIE 4 — DARK MODE
   ============================================================================

   Créer un bouton qui permet de basculer entre deux thèmes :

     ☀️ Mode clair  → body avec fond BLANC
     🌙 Mode sombre → body avec fond NOIR

   COMPORTEMENT DU BOUTON :
   - Le texte du bouton change dynamiquement :
       • "🌙 Dark Mode"  quand on est en mode clair (clic = passer en sombre)
       • "☀️ White Mode" quand on est en mode sombre (clic = passer en clair)
   - L'icône doit correspondre au mode vers lequel on bascule.

   IMPLÉMENTATION SUGGÉRÉE :
   - Utiliser une classe CSS (ex : .dark-mode) sur le <body>.
   - Faire le toggle de cette classe en jQuery avec .toggleClass().
   - Mettre à jour le texte du bouton après chaque toggle.

   ============================================================================
   PARTIE 5 — BONUS
   ============================================================================

   Sur le champ "Message", limiter dynamiquement à 300 caractères MAXIMUM.

   Suggestions :
   - Utiliser l'attribut HTML maxlength="300" (rapide mais peu visuel)
   - OU mieux : afficher un compteur en temps réel sous le champ,
     du type "143 / 300 caractères", qui se met à jour à chaque frappe.
   - Bonus du bonus : passer le compteur en rouge quand il approche
     de la limite (par exemple à partir de 280 caractères).

   ============================================================================
   CRITÈRES D'ÉVALUATION
   ============================================================================

   - Utilisation correcte de jQuery (sélecteurs, événements, manipulation DOM)
   - Validation rigoureuse de tous les champs avec les bonnes regex
   - Affichage clair des messages d'erreur et de succès
   - Dark mode fonctionnel avec changement dynamique du texte/icône
   - Code propre, indenté et commenté
   - HTML sémantique et CSS soigné
   - Bonus implémenté (compteur de caractères)

   ============================================================================
   À RENDRE
   ============================================================================

   - script.js   (toute la logique jQuery)
   - traitement.php

   Bon travail !
   ========================================================================= */

$(function () {

    
    const $form        = $("#guestbook-form");
    const $messagesBox = $("#messages");

    const $firstname = $("#firstname");
    const $lastname  = $("#lastname");
    const $usermail  = $("#usermail");
    const $postcode  = $("#postcode");
    const $phone     = $("#phone");
    const $message   = $("#message");
    const $rgpd      = $("#rgpd");

    const $counter      = $("#char-counter");
    const $toggleTheme  = $("#toggle-theme");
    const $body         = $("body");

    const regexEmail    = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
    const regexPostcode = /^\d{4}$/;
    const regexPhone    = /^04\d{8}$/; 

    function clearMessages() {
        $messagesBox.empty();
    }

    function showErrors(errors) {
        clearMessages();
        const $ul = $("<ul/>", { "class": "msg-error-list" });
        $.each(errors, function (_, err) {
            $("<li/>", { "class": "msg-error", text: err }).appendTo($ul);
        });
        $messagesBox.append($ul);
    }

    function showSuccess(text) {
        clearMessages();
        $("<p/>", { "class": "msg-success", text: text }).appendTo($messagesBox);
    }

    
    function updateCounter() {
        const len = $message.val().length;
        $counter.text(len + " / 300 caractères");
        $counter.toggleClass("counter-warn", len >= 280);
    }
    $message.on("input", updateCounter);
    updateCounter(); // état initial

  
    $form.on("submit", function (event) {

        const errors = [];

        
        const vFirstname = $.trim($firstname.val());
        const vLastname  = $.trim($lastname.val());
        const vUsermail  = $.trim($usermail.val());
        const vPostcode  = $.trim($postcode.val());
        
        const vPhone     = $phone.val().replace(/[\s.\-]/g, "");
        const vMessage   = $.trim($message.val());

        
        if (vFirstname.length < 2) {
            errors.push("Le prénom est obligatoire (2 caractères minimum).");
        }

        
        if (vLastname.length < 2) {
            errors.push("Le nom est obligatoire (2 caractères minimum).");
        }

        
        if (!regexEmail.test(vUsermail)) {
            errors.push("L'adresse e-mail n'est pas valide (ex : prenom.nom@mail.com).");
        }

        
        if (!regexPostcode.test(vPostcode)) {
            errors.push("Le code postal doit contenir exactement 4 chiffres.");
        }

        
        if (!regexPhone.test(vPhone)) {
            errors.push("Le numéro de téléphone doit commencer par 04 et contenir 10 chiffres (ex : 0498150882).");
        } else {
            
            $phone.val(vPhone);
        }

        
        if (vMessage.length < 10) {
            errors.push("Le message doit contenir au moins 10 caractères.");
        }
        if (vMessage.length > 300) {
            errors.push("Le message ne peut pas dépasser 300 caractères.");
        }

        
        if (!$rgpd.is(":checked")) {
            errors.push("Vous devez accepter le stockage de vos données personnelles.");
        }

        
        if (errors.length > 0) {
            
            event.preventDefault();
            showErrors(errors);
            
            $("html, body").animate({
                scrollTop: $messagesBox.offset().top - 80
            }, 300);
        } else {
            
            showSuccess("Formulaire valide, envoi en cours...");
        }
    });

    
    $('#toggle-theme').on('click', function () {
    $('body').toggleClass('dark-mode');
    const isDark = $('body').hasClass('dark-mode');
    $(this).text(isDark ? '☀️ White Mode' : '🌙 Dark Mode');
});
    
   

});


