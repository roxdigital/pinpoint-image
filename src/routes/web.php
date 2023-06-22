<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Statamic\Facades\Asset;

Route::get('cp/assets-fieldtype', function (Request $request) {
    try {
        $var = Asset::find($request->query('assets'))->toArray();

        return [$var];
    } catch(Exception $e) {
        return $e;
    }
});
