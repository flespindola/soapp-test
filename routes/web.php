<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
NOTA: Em produção, os erros estão configurados para apresentar uma página de erro customizada (app/Exceptions/Handler.php).
O ficheiro de erro do tipo -vue está em resources/js/pages/errors/ErrorPage.vue
Consultar documentação do inertiajs: https://inertiajs.com/error-handling
*/

Route::get('/', [DashboardController::class, 'index']);
