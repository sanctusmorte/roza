<?php

namespace App\Http\Controllers;

use App\Service\Config\RetailCrmConfigService;
use App\Service\RetailCrm\RetailCrmDataService;

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

    public function __construct(RetailCrmDataService $retailCrmDataService, RetailCrmConfigService $retailCrmConfigService, UserFiltersService $userFiltersService)
    {
        $this->retailCrmDataService = $retailCrmDataService;
        $this->retailCrmConfigService = $retailCrmConfigService;
        $this->userFiltersService = $userFiltersService;
    }

    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {

        $userFilters = $request->session()->get('userFilters', []);
//        $request->session()->remove('userFilters');

       // dd($userFilters);

        return view('map2', [
            'data' => $this->retailCrmDataService->init($userFilters),
            'config' => $this->retailCrmConfigService->getConfig(),
            'userFilters' => $userFilters,
            'baseGroupStatusFilters' => $this->userFiltersService->getBaseGroupStatuses()
        ]);
    }
}