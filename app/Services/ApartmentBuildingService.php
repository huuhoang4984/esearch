<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\ApartmentBuildingRepository;

class ApartmentBuildingService
{

    /**@var array $filterCriteria */
    protected $filterCriteria = [];

    public function __construct(
        ApartmentBuildingRepository $apartmentBuildingRepository
    )
    {
        $this->apartmentBuildingRepository = $apartmentBuildingRepository;
    }

    public function getAll()
    {
        $apartmentBuildings = $this->apartmentBuildingRepository->all();
        return $apartmentBuildings;
    }

    public function getById($id)
    {
        $apartmentBuilding = $this->apartmentBuildingRepository->find($id);
        return $apartmentBuilding;
    }
}
