<?php

namespace App\Transformers\Contacts;

use League\Fractal\TransformerAbstract;

class GroupTransformer extends TransformerAbstract
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
            'group' => $data->name,
            'links' => [
                'parent' => route('groups.index'),
                'store' => route('groups.store'),
                'show' => route('groups.show', ['group' => $data->id]),
                'update' => route('groups.update', ['group' => $data->id]),
                'destroy' => route('groups.destroy', ['group' => $data->id]),
                'contacts' => route('groups.contacts.index', ['group' => $data->id]),
            ],
        ];
    }

    public static function transformRequest($index)
    {
        $attribute = [
            'group' => 'name',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformResponse($index)
    {
        $attribute = [
            'name' => 'group',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function getOriginalAttributes($index)
    {
        $attributes = [
            'id' => 'id',
            'group' => 'name',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
