<?php

namespace App\Repositories\Eloquent;

use App\Models\ApartmentBuilding;
use App\Repositories\Contracts\ApartmentBuildingRepository;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class ApartmentBuildingRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class ApartmentBuildingRepositoryEloquent extends BaseRepository implements ApartmentBuildingRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name_en' => 'like',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ApartmentBuilding::class;
    }

    /**
     * Find specify CompanyBrand
     * This is custom Find method because the table has not a Primary key
     *
     * @param $id
     * @param  array  $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        return $this->model
            ->where('id', $id)
            ->select($columns)
            ->first();
    }
}
