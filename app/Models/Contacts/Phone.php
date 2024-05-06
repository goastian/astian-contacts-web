<?php

namespace App\Models\Contacts;

use App\Models\Master;
use App\Transformers\Contacts\PhoneTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Phone extends Master
{
    use HasFactory;

    public $table = "phones";

    //public $view = "";

    public $transformer = PhoneTransformer::class;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'number',
        'dial_code',
    ];

    protected $appends = ['full_number'];

    public function getFullNumberAttribute()
    {
        return $this->dial_code . " " . $this->number;
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
