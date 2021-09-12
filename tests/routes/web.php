<?php

use Illuminate\Support\Facades\Route;
use RenokiCo\LaravelPrerender\Test\Controllers\CrawlingController;

Route::get('/todos', [CrawlingController::class, 'todos'])->name('todos');
