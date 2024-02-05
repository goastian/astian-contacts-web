<?php

namespace App\Models\Contacts;

use App\Models\Contacts\Contact;
use App\Models\Master;
use App\Transformers\Contacts\GroupTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Master
{
    use HasFactory;

    public $table = "groups";

    //public $view = "";

    public $transformer = GroupTransformer::class;

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }
}
