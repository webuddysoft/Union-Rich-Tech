<?php

namespace App\Http\Controllers\Store\Location;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\Location\LocationCreateRequest;
use App\Http\Requests\Store\Location\LocationQueueRequest;
use App\Http\Requests\Store\Location\LocationStoreRequest;
use App\Http\Requests\Store\Location\LocationEditRequest;
use App\Http\Requests\Store\Location\LocationUpdateRequest;
use App\Http\Requests\Store\Location\LocationCheckoutRequest;
use App\Models\Store\Location\Location;
use App\Models\Store\Store;
use App\Models\Shopper\Status;
use App\Models\Shopper\Shopper;
use App\Services\Store\Location\LocationService;
use App\Services\Shopper\ShopperService;
use Illuminate\Http\Request;

/**
 * Class LocationController
 * @package App\Http\Controllers\Store
 */
class LocationController extends Controller
{
    /**
     * @var LocationService
     */
    protected $location;

    /**
     * @var Shopper
     */
    protected $shopper;

    /**
     * LocationController constructor.
     * @param LocationService $location
     */
    public function __construct(LocationService $location, ShopperService $shopper)
    {
        $this->location = $location;
        $this->shopper = $shopper;
    }   

    /**
     * @param Location $location
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function public(Request $request, Location $location)
    {
        //The last shopper information stored in session
        $shopperId = session("shopper_id", null);

        //Getting the number of active & pending shoppers
        $activeCount = $this->shopper->count([
            'location_id' => $location->id,
            'status_id' => Status::getIdByName("Active")
        ]);
        $pendingCount = $this->shopper->count([
            'location_id' => $location->id,
            'status_id' => Status::getIdByName("Pending")
        ]);

        //The top three shoppers in the pending ones
        $lastShoppers = $location->shoppers()->where('status_id', Status::getIdByName("Active"))->orderBy('check_in', 'DESC')->limit(3)->get(); 

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'location' => $location,
                'activeCount' => $activeCount,
                'pendingCount' => $pendingCount,
                'lastShoppers' => $lastShoppers->toArray()
            ]);
        } else {
            return view('stores.location.public')
                ->with('location', $location)
                ->with('activeCount', $activeCount)
                ->with('pendingCount', $pendingCount)
                ->with('lastShoppers', $lastShoppers);
        }
    }

    /**
     * @param LocationCreateRequest $request
     * @param string $storeUuid
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(LocationCreateRequest $request, string $storeUuid)
    {
        return view('stores.location.create')
            ->with('store', $storeUuid);
    }

    /**
     * @param LocationStoreRequest $request
     * @param string $storeUuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LocationStoreRequest $request, string $storeUuid): \Illuminate\Http\RedirectResponse
    {
        $this->location->create([
            'location_name' => $request->location_name,
            'shopper_limit' => $request->shopper_limit,
            'store_id' => $storeUuid
        ]);

        return redirect()->route('store.store', ['store' => $storeUuid]);
    }

    /**
     * @param LocationQueueRequest $request
     * @param string $storeUuid
     * @param string $locationUuid
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function queue(LocationQueueRequest $request, string $storeUuid, string $locationUuid)
    {
        $location = $this->location->show(
            [
                'uuid' => $locationUuid
            ],
            [
                'Shoppers',
                'Shoppers.Status'
            ]
        );

        $shoppers = null;

        if( isset($location['shoppers']) && count($location['shoppers']) >= 1 ){
            $shoppers = $this->location->getShoppers($location['shoppers']);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'shoppers' => [
                    'active' => array_values($shoppers['active']),
                    'pending' => array_values($shoppers['pending']),
                    'completed' => array_values($shoppers['completed'])
                ],
                'activeCount' => count($shoppers['active']),
                'pendingCount' => count($shoppers['pending']),
                'completedCount' => count($shoppers['completed'])
            ]);       
        } else {
            return view('stores.location.queue')
                ->with('location', $location)
                ->with('storeUuid', $storeUuid)
                ->with('statuses', Status::all())
                ->with('shoppers', $shoppers);
        }
    }

    /**
     * @param LocationEditRequest $request
     * @param string $storeUuid
     * @param string $localtionUuid
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(LocationEditRequest $request, string $storeUuid, string $locationUuid)
    {
        $location = $this->location->show(
            [
                "uuid" => $locationUuid
            ]
        );

        return view('stores.location.edit')
            ->with('store', $storeUuid)
            ->with('location', $location);
    }

    /**
     * @param LocationUpdateRequest $request
     * @param string $storeUuid
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(LocationUpdateRequest $request, string $storeUuid, string $locationUuid): \Illuminate\Http\RedirectResponse 
    {
        $location = $this->location->show(
            [
                "uuid" => $locationUuid
            ]
        );

        $this->location->update(
            $location['id'],
            [
                'location_name' => $request->location_name,
                'shopper_limit' => $request->shopper_limit
            ]
        );

        return redirect()->route('store.store', ['store' => $storeUuid]);
    }

    public function checkout(LocationCheckoutRequest $request, string $storeUuid, string $locationUuid)
    {
        $shopperId = $request->shopper_id;
        
        $this->shopper->update($shopperId, [
            'status_id' => Status::getIdByName('Completed'),
            'check_out' => date('Y-m-d H:i:s')
        ]);

        $location = $this->location->show(
            [
                'uuid' => $locationUuid
            ],
            [
                'Shoppers',
                'Shoppers.Status'
            ]
        );

        $shoppers = null;

        if( isset($location['shoppers']) && count($location['shoppers']) >= 1 ){
            $shoppers = $this->location->getShoppers($location['shoppers']);
        }
        
        return response()->json([
            'status' => 'success',
            'shoppers' => [
                'active' => array_values($shoppers['active']),
                'pending' => array_values($shoppers['pending']),
                'completed' => array_values($shoppers['completed'])
            ],
            'activeCount' => count($shoppers['active']),
            'pendingCount' => count($shoppers['pending']),
            'completedCount' => count($shoppers['completed'])
        ]);            
    }
}
