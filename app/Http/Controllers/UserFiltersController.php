<?php

namespace App\Http\Controllers;

use App\Service\UserFilters\UserFiltersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Class UserFiltersController
 * @package App\Http\Controllers
 */
class UserFiltersController extends Controller
{
    private $userFiltersService;

    public function __construct(UserFiltersService $userFiltersService)
    {
        $this->userFiltersService = $userFiltersService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $userFilters = $request->session()->get('userFilters', []);
        $userFilters = $this->userFiltersService->update($request, $userFilters);
        $request->session()->put('userFilters', $userFilters);

        return redirect()->back();
    }
}