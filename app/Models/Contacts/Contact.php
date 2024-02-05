<?php

namespace App\Models\Contacts;

use App\Models\Master;
use App\Transformers\Contacts\ContactTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Master
{
    use HasFactory;

    public $table = "contacts";

    //public $view = "";

    public $transformer = ContactTransformer::class;

    protected $fillable = [
        'name',
        'last_name',
        'address',
        'company',
    ];

}
