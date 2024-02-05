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
        ];
    }

    public static function transformRequest($index)
    {
        $attribute = [
            'nombre' => 'name',
            'correo' => 'email',
            'correo' => 'email_address'
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformResponse($index)
    {
        $attribute = [
            'name' => 'nombre',
            'email' => 'correo',
            'email_address' => 'correo'
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
