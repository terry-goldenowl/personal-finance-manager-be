<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletMonth extends Model
{
    use HasFactory;

    protected $fillable = [
        'opening_balance',
        'closing_balance',
        'wallet_id',
        'month',
        'year'
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
