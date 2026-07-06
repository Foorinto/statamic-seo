<?php

use Foorintodev\Seo\Http\Controllers\RedirectsController;
use Illuminate\Support\Facades\Route;

Route::get('foorintodev-seo/redirects', [RedirectsController::class, 'index'])
    ->name('foorintodev-seo.redirects.index');

Route::post('foorintodev-seo/redirects', [RedirectsController::class, 'store'])
    ->name('foorintodev-seo.redirects.store');

Route::delete('foorintodev-seo/redirects', [RedirectsController::class, 'destroy'])
    ->name('foorintodev-seo.redirects.destroy');
