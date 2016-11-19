@extends('template')
@section('contenu')

    <textarea readonly id="zoneAffichage" cols="60" rows="20"></textarea><br>
    <div id="choices"></div>
    <input type="text" id="input_chat">
    <button id="bouton" onclick="chat()">Envoyer</button>
    <br/>
    <button onclick="ready()">Prêt</button>
    <br/>
    <button onclick="quit()">Quitter le salon</button>
    <script type="text/javascript">
        $(document).ready(function () {
            go();
        })
    </script>

@endsection