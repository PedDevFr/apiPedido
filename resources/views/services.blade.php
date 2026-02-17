@extends('layouts.landing')

@section('title', 'Services')

@section('content')
    @component('_components.card')
        @slot('title' , 'Servicio 1');
        @slot('description' , 'Descripción del servicio 1');
    @endcomponent
@endsection