<?php
// Classe Laravel para rotas web ( Route)
use Illuminate\Support\Facades\Route;
// Classe de Livros
use App\Http\Controllers\LivroController;
Route::resource('livros', LivroController::class);


Route::get('/', function () {
    return view('welcome');
});


