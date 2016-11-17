@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="content">
            <h1>Bienvenue dans le super love letter mega bandant</h1>
            <form method="POST" action="{!! url('/') !!}" accept-charset="UTF-8">
                {!! csrf_field() !!}
                <label for="username">Entrez votre pseudo : </label>
                <input name="username" type="text" id="username">
                <input type="submit" value="Let's go !">
            </form>
        </div>
    </div>
@endsection