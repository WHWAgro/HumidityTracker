<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Fields;

class Pin extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','field_id','field_alias','razon_social','latitude','longitude','humidity','humidity_date'];

    public function field()
    {
        return $this->belongsTo(Fields::class);
    }

}
