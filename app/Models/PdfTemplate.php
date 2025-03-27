<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PdfTemplate extends Model
{
    protected $fillable = [
        'pdf_path', 'grid_pdf_path', 'ulid', 'name',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model): void {
            $model->ulid ??= Str::ulid()->toString();
        });
    }

    public function fields()
    {
        return $this->hasMany(PdfField::class, 'template_id');
    }
}
