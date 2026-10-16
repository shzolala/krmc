<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/admin/reviews', '/panel/admin/reviews')->name('review.moderate');
