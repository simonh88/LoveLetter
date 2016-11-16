@extends('template')
@section('contenu')

    <textarea id="zoneAffichage" cols="60" rows="20">Bienvenue {{$joueur}}, vous êtes dans le salon {{$idSalon}}</textarea><br>
    <input type="text" id="input">
    <button id="bouton" onclick="playtest()">Jouer</button>
    <script type="text/javascript">
        $(document).ready(function () {
            go();
        })
    </script>

@endsection