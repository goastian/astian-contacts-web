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
            'group_id' => $data->group_id ?: null,
            'grupo' => $data->group_id ? $grupo->name: null,
            'creado' => $data->created_at,
            'favorito' => $data->favorite,
            'actualizado' => $data->updated_at,
            'links' => [
                'parent' => route('contacts.index'),
                'store' => route('contacts.store'),
                'show' => route('contacts.show', ['contact' => $data->id]),
                'update' => route('contacts.update', ['contact' => $data->id]),
                'destroy' => route('contacts.destroy', ['contact' => $data->id]),
                'favorite' => route('contacts.favorite', ['id' => $data->id]),
            ],
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
            'favorito' => 'favorite'
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
            'favorite' => 'favorito'
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
            'grupo_id' => 'group_id',
            'creado' => 'created_at',
            'actualizado' => 'updated_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

}
