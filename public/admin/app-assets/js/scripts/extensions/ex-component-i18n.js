/*=========================================================================================
    File Name: i18n.js
    Description: Internationalization.
    --------------------------------------------------------------------------------------
    Item Name: Stack - Responsive Admin Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
$(document).ready(function () {
  // For Language Select
  var changeText = $('.card-localization .card-text')

  i18next.addResourceBundle('en_p', 'translation', {
    key: "Cake sesame snaps cupcake gingerbread danish I love gingerbread. Apple pie pie jujubes chupa chups muffin halvah lollipop. Chocolate cake oat cake tiramisu marzipan sugar plum. Donut sweet pie oat cake dragÃ©e fruitcake cotton candy lemon drops."
  });

  i18next.addResourceBundle('pt_p', 'translation', {
    key: "O sÃ©samo do bolo agarra dinamarquÃªs do pÃ£o-de-espÃ©cie do queque eu amo o pÃ£o-de-espÃ©cie. Torta de torta de maÃ§Ã£ jujubes chupa chups Â pirulito halvah muffin. Ameixa do aÃ§Ãºcar do maÃ§apÃ£o do tiramisu do bolo da aveia do bolo de chocolate. Donut doce aveia torta  dragÃ©e fruitcake algodÃ£o doce gotas de limÃ£o."
  });

  i18next.addResourceBundle('fr_p', 'translation', {
    key: "GÃ¢teau au sÃ©same s'enclenche petit pain au pain d'Ã©pices danois J'adore le pain d'Ã©pices. Tarte aux pommes jujubes chupa chups Â muffin halva sucette. Gateau au chocolat gateau d \ 'avoine tiramisu prune sucre. Donut tourte sucrÃ©e gateau dragÃ©e fruit gateau barbe a papa citron gouttes.."
  });
  i18next.addResourceBundle('de_p', 'translation', {
    key: "Kuchen Sesam Snaps Cupcake Lebkuchen dÃ¤nisch Ich liebe Lebkuchen. Apfelkuchen Jujubes Chupa Chups Muffin Halwa Lutscher. Schokoladenkuchen-Haferkuchen-Tiramisumarzipanzuckerpflaume. Donut sÃ¼ÃŸe Torte Hafer Kuchen DragÃ©e Obstkuchen Zuckerwatte Zitronentropfen."
  });

  // Change Card Text Language On Click
  $(".language-options").on("click", ".i18n-lang-option", function () {
    var selected_lng = $(this).data('lng');
    i18next.changeLanguage(selected_lng, function (err, t) {
      changeText.localize();
    });
  })

});