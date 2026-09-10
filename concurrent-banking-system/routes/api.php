<?php


Route::prefix('v1')->group(function () {
    require __DIR__.'/api_v1.php';
});
