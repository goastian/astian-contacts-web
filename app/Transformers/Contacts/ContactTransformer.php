<?php

namespace App\Transformers\Contacts;

use App\Models\Contacts\App;
use App\Models\Contacts\Contact;
use Elyerr\ApiResponse\Assets\Asset;
use Elyerr\ApiResponse\Assets\JsonResponser;
use League\Fractal\TransformerAbstract;

class ContactTransformer extends TransformerAbstract
{

    use JsonResponser;
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($data)
    {
        $contact = Contact::find($data->id);

        return [
            'id' => $data->id,
            'name' => $data->name,
            'last_name' => $data->last_name,
            'address' => $data->address,
            'company' => $data->company,
            'company_id' => $data->company_id,
            'created_at' => $data->created_at,
            'updated_at' => $data->updated_at,
            'favorite' => $data->favorite,
            'group_id' => $data->group_id,
            'group_name' => $contact->group_name,
            'emails' => $contact->emails()->get(),
            'phones' => $contact->phones()->get(),
            'social_networks' => $contact->apps()->get(),

            'links' => [
                'parent' => route('contacts.index'),
                'store' => route('contacts.store'),
                'show' => route('contacts.show', ['contact' => $contact->id]),
                'update' => route('contacts.update', ['contact' => $contact->id]),
                'destroy' => route('contacts.destroy', ['contact' => $contact->id]),
                'favorite' => route('contacts.favorite', ['id' => $contact->id]),
                'phone' => route('contacts.phones.index', ['contact' => $contact->id]),
                'email' => route('contacts.emails.index', ['contact' => $contact->id]),
                'site' => route('contacts.apps.index', ['contact' => $contact->id]),
            ],
        ];
    }

    public static function transformRequest($index)
    {
        $attribute = [
            'name' => 'name',
            'last_name' => 'last_name',
            'phone' => 'number',
            'dial_code' => 'dial_code',
            'email' => 'email',
            'address' => 'address',
            'company' => 'company',
            'company_id' => 'company_id',
            'group_id' => 'group_id',
            'favorite' => 'favorite',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformResponse($index)
    {
        $index = Asset::changeIndex($index);

        $attribute = [
            'name' => 'name',
            'last_name' => 'last_name',
            'dial_code' => 'dial_code',
            'number' => 'phone',
            'email' => 'email',
            'address' => 'address',
            'company' => 'company',
            'company_id' => 'company_id',
            'group_id' => 'group_id',
            'favorite' => 'favorite',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function getOriginalAttributes($index)
    {
        $attributes = [
            'id' => 'id',
            'name' => 'name',
            'last_name' => 'last_name',
            'address' => 'address',
            'company' => 'company',
            'favorite' => 'favorite',
            'grup_id' => 'group_id',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
