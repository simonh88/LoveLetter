/**
 ** Ne peut oublier de charger JQuery
 */

// Tableau contenant les cartes du joueurs
var cards = [];
var actions = [];
var numAction = 0;
var ismyturn = false;

function printMain() {
    printInfo("Voici vos cartes : ");
    cards.forEach(function (elem) {
        printInfo(" - [ " + elem['id'] + " ] " + elem['nom']);
    })
}

function printInfo(str) {
    var zoneAffichage = $('#zoneAffichage');
    zoneAffichage.val(zoneAffichage.val() + "\n" + str + "\n");
}

function printActions() {
    for (; numAction < actions.length; numAction++) {
        // afficher actions[numAction]
        var action = actions[numAction];
        if (action['type'] == 'PLAY') {
            printInfo("[+] " + action['source'] + " a joué un " + action['message']);
        }
    }
}

function myturn() {
    setTimeout(myturn, 1000);
    $.get('/myturn', function (data) {
        var res = $.parseJSON(data);

        console.log(res);



        // On set les cartes du joueur
        if (res['main']) {
            cards = res['main'];
        }


        // On check les actions
        if (res['actions']) {
            actions = res['actions'];
        }

        // On affiche les actions
        printActions();

        // On set myturn
        if (res['myturn'] && !ismyturn) {
            ismyturn = true;
            printInfo("C'est votre tour!");
            printMain();
        }


    });
}

// Nécessité de vérifier que le joueur possède bien la carte qu'il joue
function play(card) {
    if (!ismyturn) return;
    var i = cards.indexOf(card);
    if (i != -1) {
        $.get('/play/' + card['id'], function (data) {
            printInfo("Vous avez joué un " + card['nom'] + " ("  + card['id'] + ")");
            ismyturn = false;
        })
    } else {
        printInfo("Vous ne possedez pas cette carte");
    }
}

function playtest() {
    var c = cards[0]
    play(c)
}

function chat() {
    var message = $('#input').val();
}

function go () {
    myturn();
}

