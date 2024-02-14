<?php

namespace App\Http\Controllers\Contacts;

use App\Events\UpdateContactEvent;
use App\Http\Controllers\GlobalController as Controller;
use App\Models\Contacts\Contact;
use Elyerr\ApiResponse\Exceptions\ReportError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactFavoriteController extends Controller
{
    /**
     * Mark an unmark the contact as a favorite
     *
     * @param Request $request
     * @param Contact $contact
     */
    public function mark($id)
    {
        throw_unless($contact = Contact::find($id),
            new ReportError(__("Can't find the contact ..."), 403));

        throw_if($contact->user_id != $this->user()->id,
            new ReportError(__('Unauthorized user'), 403));

        DB::transaction(function () use ($contact) {

            $contact->favorite = !$contact->favorite;
            $contact->push();

            UpdateContactEvent::dispatch();
        });

        return $this->showOne($contact, $contact->transformer, 201);

    }
}
