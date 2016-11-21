@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1>Liste des salons</h1>
        </div>

        <ul class="list-group  col-md-4">
            @foreach($salons as $salon)
                <li class="list-group-item">
                    <a href="salons/{{ $salon["id"] }}">Salon n° {{ $salon["id"]  }} </a>
                    <span class="badge float-xs-right">{{ $salon["nb_joueurs_presents"] }} / {{ $salon["nb_joueurs_max"] }} </span>
                </li>
            @endforeach
        </ul>

        <form class="col-md-12" method="get" action="/clearAllSalons">
            <button class="btn btn-danger" type="submit">Clear</button>
        </form>
    </div>

@endsection