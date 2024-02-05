<?php

namespace App\Transformers\Contacts;

use App\Models\Contacts\Group;
use Elyerr\ApiResponse\Assets\Asset;
use League\Fractal\TransformerAbstract;

class ContactTransformer extends TransformerAbstract
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
        $grupo = Group::find($data->group_id);

        return [
            'id' => $data->id,
            'nombre' => $data->name,
            'apellido' => $data->last_name,
            'direccion' => $data->address,
            'empresa' => $data->company,
            'grupo' => $grupo ? $grupo->name : null,
            'creado' => $data->created_at,
            'actualizado' => $data->updated_at,
        ];
    }

    public static function transformRequest($index)
    {
        $attribute = [
            'nombre' => 'name',
            'apellido' => 'last_name',
            'direccion' => 'address',
            'empresa' => 'company',
            'grupo' => 'group_id',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function transformResponse($index)
    {
        $index = Asset::changeIndex($index);

        $attribute = [
            'name' => 'nombre',
            'last_name' => 'apellido',
            'address' => 'direccion',
            'company' => 'empresa',
            'group_id' => 'grupo',
        ];

        return isset($attribute[$index]) ? $attribute[$index] : null;
    }

    public static function getOriginalAttributes($index)
    {
        $attributes = [
            'id' => 'id',
            'nombre' => 'name',
            'apellido' => 'last_name',
            'direccion' => 'address',
            'empresa' => 'company',
            'creado' => 'created_at',
            'actualizado' => 'updated_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
