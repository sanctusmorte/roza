<?php

namespace App\Http\Controllers;

use App\Service\RetailCrm\RetailCrmDataService;
use App\Service\RetailCrm\RetailCrmOrderService;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    private $retailCrmDataService, $retailCrmOrderService;

    public function __construct(RetailCrmDataService $retailCrmDataService, RetailCrmOrderService $retailCrmOrderService)
    {
        $this->retailCrmDataService = $retailCrmDataService;
        $this->retailCrmOrderService = $retailCrmOrderService;
    }

    public function index(Request $request)
    {
       //$request->session()->put('userFilters', []);
        $userFilters = $request->session()->get('userFilters', []);

        return view('orders', [
            'data' => $this->retailCrmDataService->init($userFilters),
            'userFilters' => $userFilters
        ]);
    }

    public function update(Request $request)
    {
        $response = $this->retailCrmOrderService->updateExistOrderByOrderId($request->toArray());

        return $response;
    }
}