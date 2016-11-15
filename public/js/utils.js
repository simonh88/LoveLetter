/**
 ** Ne peut oublier de charger JQuery
 */

// Tableau contenant les cartes du joueurs
var cards = [];
var ismyturn = false;

function mylog(str) {
    var zoneAffichage = $('#zoneAffichage');
    zoneAffichage.val(zoneAffichage.val() + "\n" + str + "\n");
}

function myturn() {
    setTimeout(myturn, 1000);
    if (ismyturn) return;
    $.get('/myturn', function (data) {
        // res est un tableau
        // myturn de type booléen
        // card de type entier utilisé quand myturn est vrai
        var res = $.parseJSON(data);
        if (res['myturn']) {
            ismyturn = true;
            // C'est le tour du joueur courant
            mylog("C'est votre tour !");
            var card = res['card'];
            cards.push(card);
            mylog("Vous avez pioché un " + card);
        }
    });
}

// Nécessité de vérifier que le joueur possède bien la carte qu'il joue
function play(card) {
    if (!ismyturn) return;
    var i = cards.indexOf(card);
    console.log(cards);
    if (i != -1) {
        $.get('/play/' + card, function (data) {
            mylog("Vous avez joué un " + card);
            // Supprime la carte à l'index i, celle qui va être joué
            cards.splice(i, i);

            // Plus tard : gestion des effets
            ismyturn = false;
        })
    } else {
        mylog("Vous ne possedez pas cette carte");
    }
}

function go () {
    myturn();
}

