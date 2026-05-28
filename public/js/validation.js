$(document).ready(function () {
  

  
  $("#message").on("input", function () {
    const len = $(this).val().length;
    $("#char-counter").text(len + " / 300 caractères");
  });

  
  $("#toggle-theme").on("click", function () {
    $("body").toggleClass("dark-mode");
    const isDark = $("body").hasClass("dark-mode");
    $(this).text(isDark ? "☀️ White Mode" : "🌙 Dark Mode");
  });
});

$(function () {

    

    // ---- Validation frontend du formulaire ----
    $('#guestbook-form').on('submit', function (e) {

        $('.error-js').remove();

        var valid = true;

        var email = $('#usermail').val().trim();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email) || email.length > 120) {
            $('#usermail').after('<p class="error error-js">Email invalide ou trop long (max 120 caractères)</p>');
            valid = false;
        }

        var firstname = $('#firstname').val().trim();
        if (firstname.length < 2 || firstname.length > 100) {
            $('#firstname').after('<p class="error error-js">Prénom : entre 2 et 100 caractères</p>');
            valid = false;
        }

        var lastname = $('#lastname').val().trim();
        if (lastname.length < 2 || lastname.length > 100) {
            $('#lastname').after('<p class="error error-js">Nom : entre 2 et 100 caractères</p>');
            valid = false;
        }
        var postcode = $('#postcode').val().trim();
        if (postcode.length !== 4 || !/^\d{4}$/.test(postcode)) {
            $('#postcode').after('<p class="error error-js">Code postal : 4 chiffres uniquement</p>');
            valid = false;
        }

        var phone = $('#phone').val().trim();
        if (phone.length !== 10 || !/^\d{10}$/.test(phone)) {
            $('#phone').after('<p class="error error-js">Numéro de téléphone : 10 chiffres uniquement</p>');
            valid = false;
        }

        var message = $('#message').val().trim();
        if (message.length < 5 || message.length > 500) {
            $('#message').after('<p class="error error-js">Message : entre 5 et 500 caractères</p>');
            valid = false;
        }

        
        if (!$('#rgpd').is(':checked')) {
            $('#rgpd').after('<p class="error error-js">Veuillez accepter le stockage de vos données</p>');
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });

});