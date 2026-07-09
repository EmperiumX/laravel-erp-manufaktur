<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'leader_id',
        'monthly_target',
        'notes'
    ];

    protected $casts = [
        'monthly_target' => 'decimal:2',
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function members()
    {
        return $this->hasMany(User::class, 'sales_team_id');
    }
}
