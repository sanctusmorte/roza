<?php

namespace App\Http\Controllers;

use App\Service\Config\RetailCrmConfigService;
use App\Service\RetailCrm\RetailCrmApiClientService;
use App\Service\RetailCrm\RetailCrmMagazineService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

/**
 * Class RetailCrmSettingsController
 * @package App\Http\Controllers
 */
class RetailCrmSettingsController extends Controller
{
    private $retailCrmConfigService, $retailCrmMagazineService, $retailCrmApiClientService;

    /**
     * RetailCrmSettingsController constructor.
     * @param RetailCrmConfigService $retailCrmConfigService
     * @param RetailCrmMagazineService $retailCrmMagazineService
     * @param RetailCrmApiClientService $retailCrmApiClientService
     */
    public function __construct(RetailCrmConfigService $retailCrmConfigService, RetailCrmMagazineService $retailCrmMagazineService,
                                RetailCrmApiClientService $retailCrmApiClientService)
    {
        $this->retailCrmConfigService = $retailCrmConfigService;
        $this->retailCrmMagazineService = $retailCrmMagazineService;
        $this->retailCrmApiClientService = $retailCrmApiClientService;
    }

    public function index()
    {
        return view('config', [
            'config' => $this->retailCrmConfigService->getConfig(),
            'retailCrmMagazines' => $this->retailCrmMagazineService->getMagazines()
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make(
            $request->toArray(),
            array(
                'url' => 'required|active_url',
                'key' => 'required'
            )
        );

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        } else {
            $checkConfigResponse = $this->retailCrmApiClientService->checkCredentialsByCustomConfig($request);
            if ($checkConfigResponse['error'] !== null) {
                return Redirect::back()->with('error', $checkConfigResponse['error']);
            } else {
                $updateConfigResponse = $this->retailCrmConfigService->setConfig($request);
                return Redirect::back()->with('success', $updateConfigResponse['message']);
            }
        }
    }
}