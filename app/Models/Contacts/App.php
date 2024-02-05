<?php

namespace App\Models\Contacts;

use App\Models\Master;
use App\Transformers\Contacts\AppTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class App extends Master
{
    use HasFactory;

    public $table = "apps";

    //public $view = "";

    public $transformer = AppTransformer::class;

    protected $fillable = [
        'name',
        'uri',
    ];

}
