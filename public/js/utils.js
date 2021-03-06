/**
 ** Ne peut oublier de charger JQuery
 */

// Tableau contenant les cartes du joueurs
var cards = [];
var actions = [];
var other_players = [];
var messages_prives = [];
var numAction = 0;
var numMessagePrive = 0;
var ismyturn = false;
var username = '';
var defausses;

var protections;
var eliminations;

/**
 * Créé les boutons en fonction de cards[]
 */
function makeButtons() {
    var choices_div = $('#choices');
    choices_div.empty();
    cards.forEach(function (card) {
        choices_div.append('<button onclick="play(' + card['id'] + ')"><img style="display: inline-block;"  class="img-responsive img-rounded" width="220px" src="'+ card['image'] + '" alt="'+ card['nom'] +'"></button> ');
    })
}

function makeTabDefausses() {
    var tabDefausses = $('#tab_defausse');
    tabDefausses.empty();
    var nbTDMax = 0;
    var nbTD = 0;

    for (var key in defausses) {
        var s = "<tr>";
        s += "<th>" + key + "</th>";
        defausses[key].forEach(function (elem) {
            s += "<td>";
            s += elem['nom'];
            s += "</td>";
            nbTD += 1;

        });
        if (nbTD > nbTDMax) {
            nbTDMax = nbTD;
        } else {
            for (var i = 0; i < nbTDMax - nbTD; i++) {
                s += "<td></td>";
            }
        }
        s += "</tr>";
        tabDefausses.append(s);
        nbTD = 0;
    }
}

function makeTabEtats() {

    if (!protections || !eliminations) return;

    var tabEtat = $('#tab_etat');
    tabEtat.empty();

    var s = "";
    s += "<tr><td></td><th>Eliminé</th><th>Protégé</th>";

    // Pour itérer sur les usernames
    for (var key in eliminations) {
        s += "<tr><th>" + key + "</th><td>";
        if (eliminations[key]) {
            s += "x";
        }
        s += "</td><td>";
        if (protections[key]) {
            s += "x";
        }
        s += "</td></tr>";
    }

    tabEtat.append(s);
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

function printMessagesPrives() {
    for (; numMessagePrive < messages_prives.length; numMessagePrive++) {
        // afficher actions[numAction]
        var messagePrive = messages_prives[numMessagePrive];
        if ( messagePrive['type'] =='CHAT' ) {
            printInfo("[-PRIVATE-] " + messagePrive['source'] + " dit -> " + messagePrive['message']);
        }
    }
}

function myturn() {
    $.get('/myturn', function (data) {
        var res = $.parseJSON(data);

        //console.log(res);


        username = res['username'];

        // On set les cartes du joueur
        if (res['main'] && !ismyturn && res['main'] != cards) {
            cards = res['main'];
            makeButtons();
        }


        if (res['defausses'] && res['defausses'] != defausses) {
            defausses = res['defausses'];
            makeTabDefausses();
        }

        if (res['eliminations'] && eliminations != res['eliminations']) {
            eliminations = res['eliminations'];
            makeTabEtats();
        }

        if (res['protections'] && protections != res['protections']) {
            protections = res['protections'];
            makeTabEtats();
        }

        if (res['other_players']) {
            other_players = res['other_players'];
        }

        // On check les actions
        if (res['actions']) {
            actions = res['actions'];
        }

        if (res['messages_prives']) {
            messages_prives = res['messages_prives'];
        }

        // On affiche les actions
        printActions();
        printMessagesPrives();

        // On set myturn
        if (res['myturn'] && !ismyturn) {
            ismyturn = true;
        }


    });
}

/**
 * @param id : l'id de la carte
 * @return vrai si la carte que va joué le joueur fait partie de la liste
 */
function checkKingPrinceBaronPriestGuard(id) {
    return id != 1 && id != 2 && id != 6 && id != 7;
}

function checkGuard(id) {
    return id >= 12 && id <= 16;
}

// Nécessité de vérifier que le joueur possède bien la carte qu'il joue
function play(card_id) {

    // TODO si le joueur joue un King / Prince / Baron / Priest, il doit choisir un autre joueur pour que l'action soit effectué
    // on peut envoyer via le myturn les usernames des autres joueurs et faire pop une choice box
    // TODO Si il joue un guard, en plus de choisir un autre joueur il doit deviner une carte

    if (checkKingPrinceBaronPriestGuard(card_id)) {
        var str = "Veuillez choisir un joueur : \n ";
        other_players.forEach(function (e) {
            str += e['username'] + " ";
        });
        var joueur_cible = prompt(str);
    }

    if (checkGuard(card_id)) {
        var str = "Essayez de devinez sa carte : \nPriest Baron Handmaid Prince King Countess Princess"
        var carte_devine = prompt(str);
    }

    if (!ismyturn) {
        printInfo("[!] Ce n'est pas votre tour");
        return;
    }

    var url  = '/play/' + card_id;
    if (joueur_cible) {
        url += '/' + joueur_cible;
    }
    if (carte_devine) {
        url += '/' + carte_devine;
    }

    $.get(url, function () {    
        ismyturn = false;
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


