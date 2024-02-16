<?php

namespace App\Http\Controllers\Contacts;

use App\Events\DestroyPhoneEvent;
use App\Events\StorePhoneEvent;
use App\Events\UpdatePhoneEvent;
use App\Http\Controllers\GlobalController as Controller;
use App\Models\Contacts\Contact;
use App\Models\Contacts\Phone;
use App\Transformers\Contacts\PhoneTransformer;
use Elyerr\ApiResponse\Exceptions\ReportError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactPhoneController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.request:' . PhoneTransformer::class)->only('store', 'update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Contact $contact, Phone $phone)
    {
        $params = $this->filter_transform($phone->transformer);

        $data = $this->search($phone->table, $params, 'contact_id', $contact->id);

        return $this->showAll($data, $phone->transformer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Contact $contact, Phone $phone)
    {
        throw_unless($contact->user_id == $this->user()->id,
            new ReportError(__('Unauthorized user'), 403));

        $this->validate($request, [
            'name' => ['required', 'max:50'],
            'number' => ['required', 'max:20'],
        ]);

        DB::transaction(function () use ($request, $contact, $phone) {
            $phone = $phone->fill($request->all());
            $phone->contact_id = $contact->id;
            $phone->save();

            StorePhoneEvent::dispatch($this->user()->id);
        });

        return $this->showOne($phone, $phone->transformer, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact, Phone $phone)
    {
        throw_if($contact->user_id != $this->user()->id or $phone->contact_id != $contact->id,
            new ReportError(__('Unauthorized user'), 403));

        return $this->showOne($phone, $phone->transformer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact, Phone $phone)
    {
        throw_if($contact->user_id != $this->user()->id or $phone->contact_id != $contact->id,
            new ReportError(__('Unauthorized user'), 403));

        $this->validate($request, [
            'name' => ['max:50'],
            'number' => ['max:20'],
        ]);

        DB::transaction(function () use ($request, $contact, $phone) {

            $updated = false;

            if ($this->is_diferent($phone->name, $request->name)) {
                $updated = true;
                $phone->name = $request->name;
            }

            if ($this->is_diferent($phone->number, $request->number)) {
                $updated = true;
                $phone->number = $request->number;
            }

            if ($updated) {
                $phone->push();

                UpdatePhoneEvent::dispatch($this->user()->id);
            }

        });

        return $this->showOne($phone, $phone->transformer, 201);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact, Phone $phone)
    {
        throw_if($contact->user_id != $this->user()->id or $phone->contact_id != $contact->id,
            new ReportError(__('Unauthorized user'), 403));

        $phone->delete();

        DestroyPhoneEvent::dispatch($this->user()->id);

        return $this->showOne($phone, $phone->transformer);
    }
}
