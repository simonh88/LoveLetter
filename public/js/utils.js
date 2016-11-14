/**
 ** Ne peut oublier de charger JQuery
 */

// Tableau contenant les cartes du joueurs
var cards = [];

function mylog(str) {
    var zoneAffichage = $('#zoneAffichage');
    zoneAffichage.val(zoneAffichage.val() + str);
}

function myturn() {
    $.get('/myturn', function (data) {
        // res est un tableau
        // myturn de type booléen
        // card de type entier utilisé quand myturn est vrai
        mylog('My turn?\n');
        var res = $.parseJSON(data);
        if (res['myturn']) {
            // C'est le tour du joueur courant
            var card = res['card'];
            cards.push(card);
            console.log("New card : " + card);
        } else {
            mylog('->NO\n');
        }
    });
    setTimeout(myturn, 1000);
}

// Nécessité de vérifier que le joueur possède bien la carte qu'il joue
function play(card) {
    var i = cards.indexOf(card);
    if (i != -1) {
        $.get('/play/' + card, function (data) {
            // Supprime la carte à l'index i, celle qui va être joué
            cards.splice(i, i);

            // Plus tard : gestion des effets
        })
    }
}

function go () {
    myturn();
}

