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
        </div>



        <div class="col-md6">
            <div class="page-header">
                <h1>Vos cartes</h1>
            </div>
            <div id="choices"></div>

        </div>

        <div class="col-md-12">
            <div class="page-header">
                <h1>Actions</h1>
            </div>
            <input class="col-md-6 form-control" type="text" id="input_chat">
            <button type="button" class="col-md-3 btn btn-default form-control" id="bouton" onclick="chat()">Envoyer</button>



            <button class="col-md-6 btn btn-success" onclick="ready()">Prêt</button>
            <button class="col-md-6 btn btn-danger" onclick="quit()">Quitter le salon</button>

        </div>



        <div class="col-md-12">
            <div class="page-header">
                <h1>Status</h1>
            </div>

            <table id="tab_etat" class="table table-bordered">

            </table>
        </div>

        <div class="col-md-12">
            <div class="page-header">
                <h1>Défausse</h1>
            </div>
            <table id="tab_defausse" class="table">

            </table>
        </div>



    </div>


    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="{{ URL::asset('js/utils.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            go();
        })
    </script>

@endsection