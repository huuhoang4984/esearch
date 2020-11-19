<?php

namespace App\Services;

use App\Models\User;

class ApartmentBuildingService
{

    /**@var array $filterCriteria */
    protected $filterCriteria = [];

    public function __construct(TapiService $tapiService)
    {
        $this->tapiService = $tapiService;
    }

    /**
     * @param  User  $user
     * @param  array  $data  = [
     *     'min_price' => int,
     *     'max_price' => int,
     *     'property_types' => string,
     * ]
     *
     * @return $this
     */
    public function applyFilter(User $user, array $data = [])
    {
        $this->resetFilter();
        $filterableFields = [
            'min_price',
            'max_price',
            'property_types',
        ];

        if ($data) {
            foreach ($filterableFields as $field) {
                $this->filterCriteria[$field] = $data[$field] ?? null;
            }
        }

        $this->filterCriteria[$data['region']] = $user->getRegion($data['region']);

        $this->filterCriteria = array_filter($this->filterCriteria);

        return $this;
    }

    /**
     * @return $this
     */
    public function resetFilter()
    {
        $this->filterCriteria = [];

        return $this;
    }

    /**
     * Get number of days for report
     *
     * @param  null | User  $user
     * @return false|float|int|array
     */
    public function getNumberOfDays($user = null)
    {
        if (!$user) {
            return 0;
        }

        $brandTimeSelling = $this->tapiService->getAgentPerformanceStats(
            config('app.tapi.endpoint') . '/agent-performance/brand/time-to-sell',
            $user->brand_id,
            $this->filterCriteria
        );
        $userStats = array_get($brandTimeSelling, 'data.0.volume') ?? 0;

        $allBrands = $this->tapiService->getAgentPerformanceStats(
            config('app.tapi.endpoint') . '/agent-performance/brand/time-to-sell-all-brands',
            $user->brand_id,
            $this->filterCriteria
        );
        $allBrandsStas = array_get($allBrands, 'data.0.volume') ?? 0;

        $ceil = ceil($userStats - $allBrandsStas);

        $result = array(
            'brand_days' => $userStats,
            'diff_days' => $ceil == -0 ? 0 : $ceil,
        );
        return $result;
    }

    /**
     * Get user's ranked and top five chart
     *
     * @param  User  $user
     * @param  string  $type  will be new_instruction|sstc|exchange
     * @return int|mixed
     */
    public function getUserRankAndTopFiveChart($user, $type)
    {
        if (!$user) {
            return $this->prepareChartData($user, [], []);
        }

        switch ($type) {
            case 'new_instruction':
                $topData = $this->topNewInstruction();
                $userRank = $this->newInstructionRank($user->brand_id);
                break;

            case 'sstc':
                $topData = $this->topSSTC();
                $userRank = $this->SSTCRank($user->brand_id);
                break;

            case 'exchange':
                $topData = $this->topExchanged();
                $userRank = $this->exchangedRank($user->brand_id);
                break;

            default:
                $topData = [];
                $userRank = [];
        }

        return $this->prepareChartData($user, $topData, $userRank);
    }

    /**
     * @return array
     */
    public function topNewInstruction()
    {
        $result = $this->tapiService->getAgentPerformanceStats(
            config('app.tapi.endpoint') . '/agent-performance/rankings/new-instructions',
            null,
            $this->filterCriteria
        );
        return array_get($result, 'data', []);
    }

    /**
     * @return array
     */
    public function topSSTC()
    {
        $result = $this->tapiService->getAgentPerformanceStats(
            config('app.tapi.endpoint') . '/agent-performance/rankings/sstc',
            null,
            $this->filterCriteria
        );

        return array_get($result, 'data', []);
    }

    /**
     * @return array
     */
    public function topExchanged()
    {
        $result = $this->tapiService->getAgentPerformanceStats(
            config('app.tapi.endpoint') . '/agent-performance/rankings/exchange',
            null,
            $this->filterCriteria
        );

        return array_get($result, 'data', []);
    }

    /**
     * @param  int  $brandId
     * @return array
     */
    public function newInstructionRank($brandId)
    {
        $data = $this->tapiService->getAgentPerformanceStats(
            config('app.tapi.endpoint') . '/agent-performance/brand/new-instructions',
            $brandId,
            $this->filterCriteria
        );

        return array_get($data, 'data.0', []);
    }

    /**
     * @param  int  $brandId
     * @return array
     */
    public function SSTCRank($brandId)
    {
        $data = $this->tapiService->getAgentPerformanceStats(
            config('app.tapi.endpoint') . '/agent-performance/brand/sstc',
            $brandId,
            $this->filterCriteria
        );

        return array_get($data, 'data.0', []);
    }

    /**
     * @param  int  $brandId
     * @return array
     */
    public function exchangedRank($brandId)
    {
        $data = $this->tapiService->getAgentPerformanceStats(
            config('app.tapi.endpoint') . '/agent-performance/brand/exchange',
            $brandId,
            $this->filterCriteria
        );

        return array_get($data, 'data.0', []);
    }

    /**
     * @param  User  $user
     * @param  array  $topData
     * @param  array  $userRankData
     * @return array
     */
    protected function prepareChartData(User $user, $topData, $userRankData)
    {
        $currentRank = array_get($userRankData, 'rank', 0);
        $chart = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => '',
                    'data' => [],
                    'backgroundColor' => [],
                    'borderWidth' => 1
                ]
            ]
        ];

        $rankedBrandName = array_get($userRankData, 'brand_name');
        $primaryColor = $user->preference->primary_color ?? '#000000';
        $secondaryColor = $user->preference->secondary_color ?? '#c4c4c4';
        $topFiveBrandIds = [];

        foreach ($topData as $key => $item) {
            if(count($chart['labels']) >= 5) {
                break;
            }

            $topFiveBrandIds[] = $item['brand_id'];
            $chart['labels'][$key] = $item['brand_name'];
            $chart['datasets'][0]['data'][$key] = $item['volume'];
            $chart['datasets'][0]['backgroundColor'][$key] = $secondaryColor;
        }


        if($currentRank <= 5) {
            $index = count($chart['labels']) == 5
                ? 4
                : $currentRank < count($chart['labels']) ? $currentRank - 1 : count($chart['labels']);
            $topFiveBrandIds[] = $user->brand_id;
            $chart['datasets'][0]['backgroundColor'][$index] = $primaryColor;
            $chart['labels'][$index] = $rankedBrandName;
        }

        if (!in_array($user->brand_id, $topFiveBrandIds)) {
            $index = count($chart['labels']) == 5 ? 4 : count($chart['labels']);
            $chart['labels'][$index] = $rankedBrandName;
            $chart['datasets'][0]['data'][$index] = array_get($userRankData, 'volume', 0);
            $chart['datasets'][0]['backgroundColor'][$index] = $primaryColor;
        }

        return [
            'ranked' => $currentRank,
            'chart' => $chart
        ];
    }

    /**
     * @param  int  $brandId
     * @return array
     */
    public function propertiesSoldForGivenBrand($brandId)
    {
        $data = $this->tapiService->getAgentPerformanceStats(
            config('app.tapi.endpoint') . '/agent-performance/brand/sold-percentage',
            $brandId,
            $this->filterCriteria
        );

        return array_get($data, 'data.0', []);
    }

    /**
     * @param  int  $brandId
     * @return array
     */
    public function propertiesSoldForAllBrand($brandId)
    {
        $data = $this->tapiService->getAgentPerformanceStats(
            config('app.tapi.endpoint') . '/agent-performance/brand/sold-percentage-all-brands',
            $brandId,
            $this->filterCriteria
        );

        return array_get($data, 'data.0', []);
    }

    /**
     * @param  int  $brandId
     * @return array
     */
    public function getPercentageSold($brandId)
    {
        $brandStats = $this->propertiesSoldForGivenBrand($brandId);
        $allBrandsStats = $this->propertiesSoldForAllBrand($brandId);

        return [
            'brand_name' => array_get($brandStats, 'brand_name'),
            'percentage_sold' => array_get($brandStats, 'volume', 0) * 100,
            'area_average' => array_get($allBrandsStats, 'volume', 0) * 100,
        ];
    }

    /**
     * Get property sale difference for report
     *
     * @param  null  $user
     * @return false|float|int
     */
    public function getMonetaryValue($user = null)
    {
        if ($user) {
            $brandPropertySaleDifferenceEndpoint = config('app.tapi.endpoint') . '/agent-performance/brand/property-sale-difference';
            $brandPropertySaleDifference = $this->tapiService->getAgentPerformanceStats($brandPropertySaleDifferenceEndpoint, $user->brand_id, $this->filterCriteria);
            return array_get($brandPropertySaleDifference, 'data.0.volume', 0);
        }

        return 0;
    }
}
