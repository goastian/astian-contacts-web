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
            'link' => $data->uri,
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
