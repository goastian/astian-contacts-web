<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\GlobalController as Controller;
use App\Models\Contacts\Contact;
use App\Models\Contacts\Group;
use Elyerr\ApiResponse\Exceptions\ReportError;

class GroupContactController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Group $group, Contact $contact)
    {
        throw_unless($group->user_id == $this->user()->id,
            new ReportError(__('No cuenta con los permisos requeridos'), 403));

        $params = $this->filter_transform($contact->transformer);

        $data = $this->search($contact->table, $params, 'group_id', $group->id);

        return $this->showAll($data, $contact->transformer);
    }
}
