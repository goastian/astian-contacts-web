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
            'numero' => $data->phone,
        ];
    }

    public static function transformRequest($index)
    {
        $attribute = [
            'nombre' => 'name',
            'numero' => 'phone',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformResponse($index)
    {
        $attribute = [
            'name' => 'nombre',
            'phone' => 'numero',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function getOriginalAttributes($index)
    {
        $attributes = [
            'id' => 'id',
            'nombre' => 'name',
            'numero' => 'phone',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
