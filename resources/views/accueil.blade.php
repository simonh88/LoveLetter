@extends('template')

@section('contenu')
    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            display: table;
            font-weight: 100;
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }
        </style>
    <div class="container">
        <div class="content">
            <form method="POST" action="{!! url('salons') !!}" accept-charset="UTF-8">
                {!! csrf_field() !!}
                <label for="username">Entrez votre pseudo : </label>
                <input name="username" type="text" id="username">
                <input type="submit" value="Let's go !">
            </form>
        </div>
    </div>
@endsection