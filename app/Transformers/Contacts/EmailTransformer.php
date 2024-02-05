<?php

namespace App\Transformers\Contacts;

use League\Fractal\TransformerAbstract;

class EmailTransformer extends TransformerAbstract
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
            'correo' => $data->email,
            'links' => [
                'parent' => route('contacts.emails.index', ['contact' => $data->contact_id]),
                'store' => route('contacts.emails.store', ['contact' => $data->contact_id]),
                'show' => route('contacts.emails.show', ['contact' => $data->contact_id, 'email' => $data->id]),
                'update' => route('contacts.emails.update', ['contact' => $data->contact_id, 'email' => $data->id]),
                'destroy' => route('contacts.emails.destroy', ['contact' => $data->contact_id, 'email' => $data->id]),
            ],
        ];
    }

    public static function transformRequest($index)
    {
        $attribute = [
            'nombre' => 'name',
            'correo' => 'email_address',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformResponse($index)
    {
        $attribute = [
            'name' => 'nombre',
            'email_address' => 'correo',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function getOriginalAttributes($index)
    {
        $attributes = [
            'id' => 'id',
            'nombre' => 'name',
            'descripcion' => 'description',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
