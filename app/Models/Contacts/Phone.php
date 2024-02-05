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

    protected $fillable = [
        'name',
        'phone',
    ];

}
