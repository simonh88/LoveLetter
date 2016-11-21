@extends('layouts.app')
@section('content')

    <div class="container">


        <div class="col-md-6">
            <div class="page-header">
                <h1>Salon {{ $idSalon }}</h1>
            </div>
            <form class="form-group">
                <textarea class="form-control" readonly id="zoneAffichage" cols="60" rows="15"></textarea><br>
            </form>

            <input class="col-md-6 form-control" type="text" id="input_chat">
            <button type="button" class="col-md-3 btn btn-default form-control" id="bouton" onclick="chat()">Envoyer</button>



            <button class="col-md-6 btn btn-success" onclick="ready()">Prêt</button>
            <button class="col-md-6 btn btn-danger" onclick="quit()">Quitter le salon</button>

            <div id="choices"></div>


        </div>

        <div class="col-md-6">
            <div class="page-header">
                <h1>Défausse</h1>
            </div>
            <table class="table">
                <tr><th>Jean</th><th>Tapete</th><th>Tarlouz</th><th>Bitch</th></tr>
                <tr><td>Princess</td><td></td><td></td><td></td></tr>
                <tr><td></td><td>King</td><td></td><td></td></tr>
                <tr><td></td><td></td><td>Handmaid</td><td></td></tr>
                <tr><td></td><td></td><td></td><td>Priest</td></tr>
                <tr><td>Priest</td><td></td><td></td><td></td></tr>
            </table>
        </div>


    </div>


    <script type="text/javascript">
        $(document).ready(function () {
            go();
        })
    </script>

@endsection