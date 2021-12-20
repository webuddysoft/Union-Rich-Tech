<?php

namespace App\Models\Shopper;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Status
 * @package App\Models\Shopper
 */
class Status extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shoppers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Shopper::class);
    }

    /**
     * @param string $status
     * @return int 
     */
    public static function getIdByName($status)
    {
        return self::where('name', $status)->value("id");
    }
}
