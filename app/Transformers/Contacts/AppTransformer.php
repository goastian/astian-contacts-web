<?php

namespace App\Transformers\Contacts;

use League\Fractal\TransformerAbstract;

class AppTransformer extends TransformerAbstract
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
            'app' => $data->name,
            'uri' => $data->uri,
            'links' => [
                'parent' => route('contacts.apps.index', ['contact' => $data->contact_id]),
                'store' => route('contacts.apps.store', ['contact' => $data->contact_id]),
                'show' => route('contacts.apps.show', ['contact' => $data->contact_id, 'app' => $data->id]),
                'update' => route('contacts.apps.update', ['contact' => $data->contact_id, 'app' => $data->id]),
                'destroy' => route('contacts.apps.destroy', ['contact' => $data->contact_id, 'app' => $data->id]),
            ],
        ];
    }

    public static function transformRequest($index)
    {
        $attribute = [
            'app' => 'name',
            'link' => 'uri',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformResponse($index)
    {
        $attribute = [
            'name' => 'app',
            'uri' => 'link',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function getOriginalAttributes($index)
    {
        $attributes = [
            'id' => 'id',
            'app' => 'name',
            'link' => 'uri',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
