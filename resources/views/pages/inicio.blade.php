@extends('layouts.web')

@section('titulo')
    Sistema Web de livros
@endsection

@section('conteudo')
    @parent
    <p>Bem-vindo ao Sistema Web de Livros. Aqui você pode gerenciar sua coleção de livros de forma fácil e eficiente.</p>
@endsection

@section('lista_livros')
    <button class="btn btn-primary">Ver lista de livros</button> 
@endsection

@section('rodape')
    Sistema Web de Livros - 2024
@endsection