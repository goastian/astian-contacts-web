<?php

namespace App\Models\Contacts;

use App\Models\Contacts\App;
use App\Models\Contacts\Email;
use App\Models\Contacts\Group;
use App\Models\Contacts\Phone;
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
        'group_id',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function emails()
    {
        return $this->hasMany(Email::class);
    }

    public function apps()
    {
        return $this->hasMany(App::class);
    }

    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

}
