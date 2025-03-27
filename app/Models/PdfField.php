<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfField extends Model
{
    protected $fillable = ['template_id', 'field_name', 'x', 'y'];

    public function template()
    {
        return $this->belongsTo(PdfTemplate::class);
    }
}
