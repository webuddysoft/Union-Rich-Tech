<?php

namespace App\Http\Controllers\Shopper;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Shopper\ShopperService;
use App\Models\Shopper\Status;
use App\Models\Store\Location\Location;
use App\Http\Requests\ShopperCheckinRequest;

/**
 * Class ShopperQueueController
 * @package App\Http\Controllers\Shopper
 */
class ShopperQueueController extends Controller
{
    /**
     * @var ShopperService
     */
    protected $shopper;

    /**
     * ShopperQueueController constructor.
     * @param ShopperService $shopper
     */
    public function __construct(ShopperService $shopper)
    {
        $this->shopper = $shopper;
    }

    public function checkIn(ShopperCheckinRequest $request, Location $location) {
        $activeStatus = Status::getIdByName('Active');
        $pendingStatus = Status::getIdByName('Pending');

        $activeCount = $location->shoppers()->where('status_id', $activeStatus)->count();
        $newShopper = $this->shopper->create([
            'location_id' => $location->id,
            'status_id' => $activeCount < $location->shopper_limit ? $activeStatus : $pendingStatus,
            'check_in' => date('Y-m-d H:i:s'),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email
        ]);

        //Store shopper id to session
        session(['shopper_id' => $newShopper['id']]);

        return redirect()->route('public.location', ['location' => $location->uuid]);
    }

}
