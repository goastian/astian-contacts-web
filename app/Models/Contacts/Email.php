<?php

namespace App\Models\Contacts;

use App\Models\Master;
use App\Transformers\Contacts\EmailTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Email extends Master
{
    use HasFactory;

    public $table = "emails";

    //public $view = "";

    public $transformer = EmailTransformer::class;

    public $timestamps = false;

    protected $fillable = [
        'email',
        'name',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
