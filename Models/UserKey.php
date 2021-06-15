<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class UserKey extends Model
{
    protected $fillable = [
        'platform_id',
        'key',
        'slug',
    ];

    public function getKeyAttribute($value): ?string
    {
        return empty($value) ? null : Crypt::decryptString($value);
    }

    public function setKeyAttribute($value): void
    {
        $this->attributes['key'] = Crypt::encryptString($value);
    }
}
