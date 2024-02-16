<?php

namespace App\Http\Controllers\Contacts;

use App\Events\DestroyGroupEvent;
use App\Events\StoreGroupEvent;
use App\Events\UpdateGroupEvent;
use App\Http\Controllers\GlobalController as Controller;
use App\Models\Contacts\Contact;
use App\Models\Contacts\Group;
use App\Transformers\Contacts\GroupTransformer;
use Elyerr\ApiResponse\Exceptions\ReportError;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('transform.request:' . GroupTransformer::class)->only('store', 'update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Group $group)
    {
        $params = $this->filter_transform($group->transformer);

        $data = $this->search($group->table, $params, 'user_id', $this->user()->id);

        return $this->showAll($data, $group->transformer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Group $group)
    {
        throw_if($this->search($group->table, null, 'user_id', $this->user()->id)->contains('name', $request->name),
            new ReportError(__('El grupo ya ha sido registrado'), 422));

        $this->validate($request, [
            'name' => ['required', 'max:100'],
        ]);

        DB::transaction(function () use ($request, $group) {
            $group = $group->fill($request->only('name'));
            $group->user_id = $this->user()->id;
            $group->save();

            StoreGroupEvent::dispatch($this->user()->id);
        });

        return $this->showOne($group, $group->transformer, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        throw_if($group->user_id != $this->user()->id,
            new ReportError(__('Unauthorized user'), 403));

        return $this->showOne($group, $group->transformer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        throw_if($group->user_id != $this->user()->id,
            new ReportError(__('Unauthorized user'), 403));

        $this->validate($request, [
            'name' => ['max:100'],
        ]);

        DB::transaction(function () use ($request, $group) {

            if ($this->is_diferent($group->name, $request->name)) {
                $group->name = $request->name;

                $group->push();

                UpdateGroupEvent::dispatch($this->user()->id);
            }
        });

        return $this->showOne($group, $group->transformer, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group, Contact $contact)
    {
        throw_if($group->user_id != $this->user()->id,
            new ReportError(__('Unauthorized user'), 403));

        DB::transaction(function () use ($group, $contact) {

            try {
                DB::table($contact->table)->where('group_id', '=', $group->id)->update([
                    'group_id' => null,
                ]);

            } catch (QueryException $e) {}

            $group->delete();

            DestroyGroupEvent::dispatch($this->user()->id);
        });

        return $this->showOne($group, $group->transformer);
    }
}
