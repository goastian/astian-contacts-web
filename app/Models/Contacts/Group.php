<?php

namespace App\Models\Contacts;

use App\Models\Master;
use App\Transformers\Contacts\GroupTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Master
{
    use HasFactory;

    public $table = "groups";

    //public $view = "";

    public $transformer = GroupTransformer::class;

    protected $fillable = [
        'name',
    ];

}
