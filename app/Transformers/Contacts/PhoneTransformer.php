<?php

namespace App\Transformers\Contacts;

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
        return [
            'id' => $data->id,
            'nombre' => $data->name,
            'numero' => $data->number,
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
            'nombre' => 'name',
            'numero' => 'number',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformResponse($index)
    {
        $attribute = [
            'name' => 'nombre',
            'number' => 'numero',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function getOriginalAttributes($index)
    {
        $attributes = [
            'id' => 'id',
            'nombre' => 'name',
            'numero' => 'number',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
