<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['zip', 'label'])]
class ServiceZip extends Model
{
    public static function allows(string $zip): bool
    {
        // If no ZIPs are configured, allow all (open service area)
        if (static::count() === 0) {
            return true;
        }

        return static::where('zip', $zip)->exists();
    }
}
