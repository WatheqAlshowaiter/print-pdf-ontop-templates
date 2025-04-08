<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdfTemplateField extends Model
{
    protected $fillable = [
        'pdf_template_id', 'value', 'x', 'y',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(PdfTemplate::class, 'pdf_template_id');
    }
}
