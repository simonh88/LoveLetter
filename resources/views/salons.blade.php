@extends('template')
@section('contenu')

    <textarea readonly id="zoneAffichage" cols="60" rows="20">Bienvenue {{$joueur}}, vous êtes dans le salon {{$idSalon}}</textarea><br>
    <div id="choices">

    </div>
    <input type="text" id="input_chat">
    <button id="bouton" onclick="chat()">Envoyer</button>
    <script type="text/javascript">
        $(document).ready(function () {
            go();
        })
    </script>

@endsection