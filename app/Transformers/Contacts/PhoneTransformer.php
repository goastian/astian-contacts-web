<?php

namespace App\Transformers\Contacts;

use App\Models\Contacts\Phone;
use League\Fractal\TransformerAbstract;

class PhoneTransformer extends TransformerAbstract
{
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
        $phone =  Phone::find($data->id);

        return [
            'id' => $data->id,
            'name' => $data->name,
            'dial_code' => $data->dial_code,
            'number' => $data->number,
            'full_number' => $phone->full_number,
            'links' => [
                'parent' => route('contacts.phones.index', ['contact' => $data->contact_id]),
                'store' => route('contacts.phones.store', ['contact' => $data->contact_id]),
                'show' => route('contacts.phones.show', ['contact' => $data->contact_id, 'phone' => $data->id]),
                'update' => route('contacts.phones.update', ['contact' => $data->contact_id, 'phone' => $data->id]),
                'destroy' => route('contacts.phones.destroy', ['contact' => $data->contact_id, 'phone' => $data->id]),
            ],
        ];
    }

    public static function transformRequest($index)
    {
        $attribute = [
            'name' => 'name',
            'dial_code' => 'dial_code',
            'number' => 'number',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformResponse($index)
    {
        $attribute = [
            'name' => 'name',
            'dial_code' => 'dial_code',
            'number' => 'number',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function getOriginalAttributes($index)
    {
        $attributes = [
            'id' => 'id',
            'name' => 'name',
            'dial_code' => 'dial_code',
            'number' => 'number',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
