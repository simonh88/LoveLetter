@extends('template')
@section('contenu')

    <h1>Liste des salons</h1>
    <ul>
            @foreach($salons as $salon)
                <li><a href="salons/{{ $salon["id"] }}">Salon n° {{ $salon["id"]  }} </a>-- {{ $salon["nb_joueurs_presents"] }} / {{ $salon["nb_joueurs_max"] }}</li>
            @endforeach
    </ul>


@endsection