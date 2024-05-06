<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\GlobalController as Controller;
use App\Models\Contacts\App;
use App\Models\Contacts\Contact;
use App\Models\Contacts\Email;
use App\Models\Contacts\Group;
use App\Models\Contacts\Phone;
use App\Transformers\Contacts\ContactTransformer;
use Elyerr\ApiResponse\Exceptions\ReportError;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.request:' . ContactTransformer::class)->only('store', 'update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Contact $contact)
    {
        $params = $this->filter_transform($contact->transformer);

        $data = $this->search($contact->table, $params, 'user_id', $this->user()->id);

        return $this->showAll($data, $contact->transformer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Contact $contact, Group $group, Email $email, Phone $phone)
    {
        $data = $this->search($group->table, null, 'user_id', $this->user()->id)->pluck('id');

        $this->validate($request, [
            'name' => ['required', 'max:100'],
            'last_name' => ['required', 'max:100'],
            'dial_code' => ['required', 'max:10'],
            'number' => ['required', 'max:20'],
            'email' => ['required', 'email', 'max:100'],
            'address' => ['nullable', 'max:150'],
            'company' => ['nullable', 'max:150'],
            'company_id' => ['nullable', 'max:150'],
            'group_id' => ['nullable', Rule::in($data)],
            'favorite' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($request, $contact, $phone, $email) {
            $contact = $contact->fill($request->except('phone', 'email'));
            $contact->user_id = $this->user()->id;
            $contact->save();

            $phone->name = "Personal";
            $phone->number = $request->number;
            $phone->dial_code = $request->dial_code;
            $phone->contact_id = $contact->id;
            $phone->save();

            $email->name = "Personal";
            $email->email = $request->email;
            $email->contact_id = $contact->id;
            $email->save();

            $this->privateChannel("StoreContactEvent", "New contatc created", config('echo-client.channel') . "." . $this->user()->id);

        });

        return $this->showOne($contact, $contact->transformer, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        throw_if($contact->user_id != $this->user()->id,
            new ReportError(__('No cuenta con los permisos requeridos'), 403));

        return $this->showOne($contact, $contact->transformer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact, Group $group)
    {
        throw_if($contact->user_id != $this->user()->id,
            new ReportError(__('Unauthorized user'), 403));

        $data = $this->search($group->table, null, 'user_id', $this->user()->id)->pluck('id');

        $this->validate($request, [
            'name' => ['nullable', 'max:100'],
            'last_name' => ['nullable', 'max:100'],
            'address' => ['nullable', 'max:150'],
            'company' => ['nullable', 'max:150'],
            'company_id' => ['nullable', 'max:150'],
            'group_id' => ['nullable', Rule::in($data)],
            'favorite' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($request, $contact) {
            $updated = false;

            if ($this->is_diferent($contact->name, $request->name)) {
                $updated = true;
                $contact->name = $request->name;
            }

            if ($this->is_diferent($contact->last_name, $request->last_name)) {
                $updated = true;
                $contact->last_name = $request->last_name;
            }

            if ($this->is_diferent($contact->address, $request->address)) {
                $updated = true;
                $contact->address = $request->address;
            }

            if ($this->is_diferent($contact->company, $request->company)) {
                $updated = true;
                $contact->company = $request->company;
            }

            if ($this->is_diferent($contact->company_id, $request->company_id)) {
                $updated = true;
                $contact->company_id = $request->company_id;
            }

            if ($this->is_diferent($contact->group_id, $request->group_id)) {
                $updated = true;
                $contact->group_id = $request->group_id;
            }

            if ($contact->favorite != $request->favorite) {
                $updated = true;
                $contact->favorite = $request->favorite;
            }

            if ($updated) {
                $contact->push();

                $this->privateChannel("UpdateContactEvent", "Contact updated", config('echo-client.channel') . "." . $this->user()->id);

            }

        });

        return $this->showOne($contact, $contact->transformer, 201);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact, Email $email, App $app, Group $group, Phone $phone)
    {
        throw_if($contact->user_id != $this->user()->id,
            new ReportError(__('Unauthorized user'), 403));

        DB::transaction(function () use ($contact, $email, $app, $group, $phone) {
            try {
                DB::table($email->table)->where('contact_id', '=', $contact->id)->delete();
            } catch (QueryException $e) {
            }

            try {
                DB::table($app->table)->where('contact_id', '=', $contact->id)->delete();
            } catch (QueryException $e) {
            }

            try {
                DB::table($phone->table)->where('contact_id', '=', $contact->id)->delete();

            } catch (QueryException $e) {

            }

            $contact->delete();

            $this->privateChannel("DestroyContactEvent", "Contact deleted", config('echo-client.channel') . "." . $this->user()->id);

        });

        return $this->message("Contact deleted", 201);
    }
}
