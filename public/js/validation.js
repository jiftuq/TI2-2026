$(document).ready(function () {
  const REGEX = {
    // regex pour nom et prénom
    username: /^[a-zA-Z]{3,100}$/,
    // regex pour adresse mail
    email: /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/,
    // regex pour code postal
    postCode: /^[0-9]{4}$/,
    // regex pour numero gsm
    gsm: /^(?:\+324|00324|04)\d{8}$/,
  };

  // verification du firstname en temps réel
  $("#firstname").on("keyup", function () {
    // Ton code ici ↓
    const val = $(this).val();
    const $field = $("#f-firstname");
    const $hint = $field.find(".hint");

    $field.removeClass("ok error");

    if (val === "") {
      $hint.text("3 à 100 caractères : lettres ");
      return;
    }

    if (REGEX.username.test(val)) {
      $field.addClass("ok");
      $hint.text("prenom valide");
    } else {
      $field.addClass("error");
      $hint.text("✗ 3-100 caractères, uniquement lettres");
    }
  });
  // verification du lastname en temps réel
  $("#lastname").on("keyup", function () {
    // Ton code ici ↓
    const val = $(this).val();
    const $field = $("#f-lastname");
    const $hint = $field.find(".hint");

    $field.removeClass("ok error");

    if (val === "") {
      $hint.text("3 à 100 caractères : lettres ");
      return;
    }

    if (REGEX.username.test(val)) {
      $field.addClass("ok");
      $hint.text("prenom valide");
    } else {
      $field.addClass("error");
      $hint.text("✗ 3-100 caractères, uniquement lettres");
    }
  });

  /* ============================================================
  VALIDATION TEMPS RÉEL — email
  ============================================================ */

  $("#usermail").on("keyup", function () {
    // Ton code ici ↓
    const val = $(this).val();
    const $field = $("#f-email");
    const $hint = $field.find(".hint");

    $field.removeClass("ok error");

    if (val === "") {
      $hint.text("Format : nom@domaine.ext");
      return;
    }

    if (REGEX.email.test(val)) {
      $field.addClass("ok");
      $hint.text("Email valide");
    } else {
      $field.addClass("error");
      $hint.text("✗ Format invalide (ex : nom@domaine.com)");
    }
  });

  // validation de code postal
  $("#postcode").on("keyup", function () {
    // Ton code ici ↓
    const val = $(this).val();
    const $field = $("#f-postcode");
    const $hint = $field.find(".hint");

    $field.removeClass("ok error");

    if (val === "") {
      $hint.text("doit etre composé de 4 chiffre");
      return;
    }

    if (REGEX.postCode.test(val)) {
      $field.addClass("ok");
      $hint.text("code postal valide");
    } else {
      $field.addClass("error");
      $hint.text("✗ Format invalide (ex : 1080)");
    }
  });

  // validation du numero de gsm
  $("#phone").on("keyup", function () {
    // Ton code ici ↓
    const val = $(this).val();
    const $field = $("#f-phone");
    const $hint = $field.find(".hint");

    $field.removeClass("ok error");

    if (val === "") {
      $hint.text("doit etre composé de 8 chiffre apres le 4");
      return;
    }

    if (REGEX.gsm.test(val)) {
      $field.addClass("ok");
      $hint.text("gsm valide");
    } else {
      $field.addClass("error");
      $hint.text("✗ Format invalide (ex : 00324** ou +324*** ou 04***)");
    }
  });
  $('#toggle-theme').on('click', function () {
    $('body').toggleClass('dark-mode');
    const isDark = $('body').hasClass('dark-mode');
    $(this).text(isDark ? '☀️ White Mode' : '🌙 Dark Mode');
});
});