<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Service\RetailCrm\Courier\RetailCrmCourierService;
use App\Service\RetailCrm\RetailCrmDataService;
use App\Service\RetailCrm\RetailCrmOrderService;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

/**
 * Class CouriersController
 * @package App\Http\Controllers
 */
class CouriersController extends Controller
{
    private $retailCrmCourierService, $retailCrmDataService, $retailCrmOrderService;

    public function __construct(RetailCrmCourierService $retailCrmCourierService, RetailCrmDataService $retailCrmDataService,
                                RetailCrmOrderService $retailCrmOrderService)
    {
        $this->retailCrmCourierService = $retailCrmCourierService;
        $this->retailCrmDataService = $retailCrmDataService;
        $this->retailCrmOrderService = $retailCrmOrderService;
    }

    /**
     * @return View
     */
    public function index()
    {
        $this->retailCrmCourierService->getCouriers();

        return view('couriers', [
            'couriers' => Courier::paginate(12)
        ]);
    }


    /**
     * @param Request $request
     * @return array
     */
    public function update(Request $request): array
    {
        return $this->retailCrmCourierService->setNewColorForExistCourier($request->toArray());
    }

    /**
     * @param Request $request
     * @return array
     */
    public function updateStatus(Request $request): array
    {
        return $this->retailCrmCourierService->setStatusForExistCourier($request->toArray());
    }
}