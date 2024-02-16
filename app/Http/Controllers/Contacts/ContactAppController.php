<?php

namespace App\Http\Controllers\Contacts;

use App\Events\DestroyAppEvent;
use App\Events\StoreAppEvent;
use App\Events\UpdateAppEvent;
use App\Http\Controllers\GlobalController as Controller;
use App\Models\Contacts\App;
use App\Models\Contacts\Contact;
use App\Transformers\Contacts\AppTransformer;
use Elyerr\ApiResponse\Exceptions\ReportError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactAppController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.request:' . AppTransformer::class)->only('update', 'store');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Contact $contact, App $app)
    {
        $params = $this->filter_transform($app->transformer);

        $data = $this->search($app->table, $params, 'contact_id', $contact->id);

        return $this->showAll($data, $app->transformer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Contact $contact, App $app)
    {
        throw_unless($contact->user_id == $this->user()->id,
            new ReportError(__('Unauthorized user'), 403));

        $this->validate($request, [
            'name' => ['required', 'max:50'],
            'uri' => ['required', 'url:http,https'],
        ]);

        DB::transaction(function () use ($request, $contact, $app) {

            $app = $app->fill($request->all());
            $app->contact_id = $contact->id;
            $app->save();

            StoreAppEvent::dispatch($this->user()->id);
        });

        return $this->showOne($app, $app->transformer, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact, App $app)
    {
        throw_if($contact->user_id != $this->user()->id or $contact->id != $app->contact_id,
            new ReportError(__('Unauthorized user'), 403));

        return $this->showOne($app);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact, App $app)
    {
        throw_if($contact->user_id != $this->user()->id or $contact->id != $app->contact_id,
            new ReportError(__('Unauthorized user'), 403));

        $this->validate($request, [
            'name' => ['max:50'],
            'uri' => ['url:http,https'],
        ]);

        DB::transaction(function () use ($request, $app) {

            $updated = false;

            if ($this->is_diferent($app->name, $request->name)) {
                $updated = true;
                $app->name = $request->name;
            }

            if ($this->is_diferent($app->uri, $request->uri)) {
                $updated = true;
                $app->uri = $request->uri;
            }

            if ($updated) {
                $app->push();

                UpdateAppEvent::dispatch($this->user()->id);
            }

        });

        return $this->showOne($app, $app->transformer, 201);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact, App $app)
    {
        throw_if($contact->user_id != $this->user()->id or $contact->id != $app->contact_id,
            new ReportError(__('Unauthorized user'), 403));

        $app->delete();

        DestroyAppEvent::dispatch($this->user()->id);

        return $this->showOne($app, $app->transformer);
    }
}