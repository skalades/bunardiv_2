<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'company_name', 'company_address', 'company_phone', 'company_email', 'company_logo', 'bank_accounts', 'invoice_notes'
    ];

    protected $casts = [
        'bank_accounts' => 'array',
    ];
}
