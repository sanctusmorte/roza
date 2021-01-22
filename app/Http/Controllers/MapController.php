<?php

namespace App\Http\Controllers;

use App\Service\Config\RetailCrmConfigService;
use App\Service\RetailCrm\RetailCrmDataService;

use App\Service\RetailCrm\RetailCrmStatusService;
use App\Service\UserFilters\UserFiltersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

/**
 * Class MapController
 * @package App\Http\Controllers
 */
class MapController extends Controller
{
    private $retailCrmDataService ,$retailCrmConfigService, $userFiltersService;
    private $retailCrmStatusService;

    public function __construct(RetailCrmDataService $retailCrmDataService, RetailCrmConfigService $retailCrmConfigService,
                                UserFiltersService $userFiltersService, RetailCrmStatusService $retailCrmStatusService)
    {
        $this->retailCrmDataService = $retailCrmDataService;
        $this->retailCrmConfigService = $retailCrmConfigService;
        $this->userFiltersService = $userFiltersService;
        $this->retailCrmStatusService = $retailCrmStatusService;
    }

    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        //$request->session()->remove('userFilters');
        $userFilters = $request->session()->get('userFilters', []);


        $baseGroupStatusFilters = $this->userFiltersService->getBaseGroupStatuses();


        return view('map2', [
            'data' => $this->retailCrmDataService->init($userFilters),
            'config' => $this->retailCrmConfigService->getConfig(),
            'userFilters' => $userFilters,
            'statuses' => $this->retailCrmStatusService->getStatusesByGroups($baseGroupStatusFilters),
            'baseGroupStatusFilters' => $baseGroupStatusFilters
        ]);
    }
}