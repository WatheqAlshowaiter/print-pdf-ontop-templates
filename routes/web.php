<?php

use App\Http\Controllers\PDFTemplateController;
use App\Http\Controllers\PrintPdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => to_route('pdf-templates.index'));

Route::resource('pdf-templates', PDFTemplateController::class)
    ->only(['create', 'store', 'destroy', 'index', 'show']);

Route::resource('print-pdfs', PrintPDFController::class)
    ->only(['edit', 'update']);
