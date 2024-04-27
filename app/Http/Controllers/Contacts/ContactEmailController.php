<?php

namespace App\Http\Controllers\Contacts;

use App\Events\DestroyEmailEvent;
use App\Http\Controllers\GlobalController as Controller;
use App\Models\Contacts\Contact;
use App\Models\Contacts\Email;
use App\Transformers\Contacts\EmailTransformer;
use Elyerr\ApiResponse\Exceptions\ReportError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactEmailController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.request:' . EmailTransformer::class)->only('store', 'update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Contact $contact, Email $email)
    {
        $params = $this->filter_transform($email->transformer);

        $data = $this->search($email->table, $params, 'contact_id', $contact->id);

        return $this->showAll($data, $email->transformer);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Contact $contact, Email $email)
    {
        throw_unless($contact->user_id == $this->user()->id,
            new ReportError(__('Unauthorized user'), 403));

        $this->validate($request, [
            'email_address' => ['required', 'email', 'max:100'],
            'name' => ['required', 'max:100'],
        ]);

        DB::transaction(function () use ($request, $contact, $email) {
            $email = $email->fill($request->all());
            $email->email = $request->email_address;
            $email->contact_id = $contact->id;
            $email->save();

            $this->privateChannel("StoreEmailEvent", "New email created", config('echo-client.channel') . "." . $this->user()->id);
        });

        return $this->showOne($email, $email->transformer, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact, Email $email)
    {
        throw_if($contact->user_id != $this->user()->id or $contact->id != $email->contact_id,
            new ReportError(__('Unauthorized user'), 403));

        return $this->showOne($email, $email->transformer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact, Email $email)
    {
        throw_if($contact->user_id != $this->user()->id or $contact->id != $email->contact_id,
            new ReportError(__('Unauthorized user'), 403));

        $this->validate($request, [
            'email_address' => ['email', 'max:100'],
            'name' => ['max:100'],
        ]);
        DB::transaction(function () use ($request, $email) {
            $updated = false;

            if ($this->is_diferent($email->name, $request->name)) {
                $updated = true;
                $email->name = $request->name;
            }

            if ($this->is_diferent($email->email, $request->email_address)) {
                $updated = true;
                $email->email = $request->email_address;
            }

            if ($updated) {
                $email->push();

                $this->privateChannel("UpdateEmailEvent", "Email updated", config('echo-client.channel') . "." . $this->user()->id);

            }

        });

        return $this->showOne($email, $email->transformer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact, Email $email)
    {
        throw_if($contact->user_id != $this->user()->id or $contact->id != $email->contact_id,
            new ReportError(__('Unauthorized user'), 403));

        $email->delete();

        $this->privateChannel("DestroyEmailEvent", "Email deleted", config('echo-client.channel') . "." . $this->user()->id);

        return $this->showOne($email, $email->transformer);
    }
}
