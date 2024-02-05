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
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
