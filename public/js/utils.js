/**
 ** Ne peut oublier de charger JQuery
 */

// Tableau contenant les cartes du joueurs
var cards = [];
var actions = [];
var numAction = 0;
var ismyturn = false;
var username = '';

/**
 * Créé les boutons en fonction de cards[]
 */
function makeButtons() {
    var choices_div = $('#choices');
    choices_div.empty();
    cards.forEach(function (card) {
        choices_div.append('<button onclick="play('+ card['id'] +')">'+ card['nom'] +'</button>');
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
            if (action['source'] == username) {
                printInfo("[+] Vous avez joué un " + action['message']);
            } else {
                printInfo("[+] " + action['source'] + " a joué un " + action['message']);
            }
        }

        if ( action['type'] =='CHAT' ) {
            printInfo("[-] " + action['source'] + " dit -> " + action['message']);
        }
    }
}

function myturn() {
    $.get('/myturn', function (data) {
        var res = $.parseJSON(data);

        //console.log(res);


        username = res['username'];

        // On set les cartes du joueur
        if (res['main']) {
            cards = res['main'];
        }

        // Créer les boutons
        makeButtons();


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
        }


    });
}

// Nécessité de vérifier que le joueur possède bien la carte qu'il joue
function play(card_id) {
    if (!ismyturn) {
        printInfo("[!] Ce n'est pas votre tour");
        return;
    }

    $.get('/play/' + card_id, function () {
        ismyturn = false;
        myturn();
    });

}

function chat() {
    var input_chat = $('#input_chat');
    var message = input_chat.val();
    input_chat.val('');
    $.get('/chat/'+message, function (data) {
        console.log(data);
    });
}

function go () {
    setInterval(myturn, 1000);
    myturn();
}

function quit() {
    $.get('/quit', function () {

    });
    window.location.href = "/";
}

function ready() {
    $.get('/ready', function () {

    });
}

