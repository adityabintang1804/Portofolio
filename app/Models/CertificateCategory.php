<?php

namespace App\Models;

use Database\Factories\CertificateCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateCategory extends Model
{
    /** @use HasFactory<CertificateCategoryFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class)->orderBy('display_order');
    }
}
