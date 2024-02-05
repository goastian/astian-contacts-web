<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\GlobalController as Controller;
use App\Models\Contacts\Contact;
use App\Models\Contacts\Group;
use App\Transformers\Contacts\ContactTransformer;
use Elyerr\ApiResponse\Exceptions\ReportError;
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
    public function store(Request $request, Contact $contact, Group $group)
    {
        $data = $this->search($group->table, null, 'user_id', $this->user()->id)->pluck('id');

        $this->validate($request, [
            'name' => ['required', 'max:100'],
            'last_name' => ['required', 'max:100'],
            'address' => ['max:150'],
            'company' => ['max:150'],
            'group_id' => [Rule::in($data)],
        ]);

        DB::transaction(function () use ($request, $contact, $group) {
            $contact = $contact->fill($request->all());
            $contact->user_id = $this->user()->id;
            $contact->save();
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
            new ReportError(__('No cuenta con los permisos requeridos'), 403));

        $data = $this->search($group->table, null, 'user_id', $this->user()->id)->pluck('id');

        $this->validate($request, [
            'name' => ['max:100'],
            'last_name' => ['max:100'],
            'address' => ['max:150'],
            'company' => ['max:150'],
            'group_id' => [Rule::in($data)],
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

            if ($this->is_diferent($contact->group_id, $request->group_id)) {
                $updated = true;
                $contact->group_id = $request->group_id;
            }

            if ($updated) {
                $contact->push();
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
    public function destroy(Contact $contact)
    {
        throw_if($contact->user_id != $this->user()->id,
            new ReportError(__('No cuenta con los permisos requeridos'), 403));

        $contact->delete();

        return $this->showOne($contact, $contact->transformer);
    }
}
