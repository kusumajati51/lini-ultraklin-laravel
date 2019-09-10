<?php

namespace App\Utils;

use App\V1\UserStore;
use App\V1\StoreOrderHistory;

use App\Utils\FirebaseUtil;

class StoreUtil {
    public function findNearby($location)
    {
        $radius = 10; // In kilometer

        $stores = UserStore::selectRaw(
            '*, ( 6371 * acos( cos( radians(?) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(?) ) + sin( radians(?) ) * sin( radians( lat ) ) ) ) AS distance',
            [
                $location['lat'],
                $location['lng'],
                $location['lat']
            ]
        )
        ->having('distance', '<', $radius)
        ->where('region_id', '!=', null)
        ->where('active', 1)
        ->orderBy('distance')
        ->take(10)
        ->get();

        return $stores;
    }

    public function getStoresForAssign($order)
    {
        $stores = $this->findNearby(['lat' => $order->lat, 'lng' => $order->lng])
            ->filter(function ($store) use ($order) {
                $order = $store->orderHistories()
                    ->select('order_id')
                    ->where('order_id', $order->id)
                    ->first();

                return $order == null;
            });

        return $stores;
    }

    public function findStoreForAssign($order)
    {
        $stores = (new self)->getStoresForAssign($order);
        $storesCount = $stores->count();

        if ($storesCount == 0) return null;

        $currentStoreIndex = 0;

        foreach ($stores as $store) {
            $currentStoreIndex += 1;

            $storePackages = $store->packages()->pluck('name')->toArray();
            $storeHavingPackage = in_array($order->package, $storePackages);
            $isLastStoreIndex = $currentStoreIndex == $storesCount;

            if (!$storeHavingPackage && !$isLastStoreIndex) {
                continue;
            } else if (!$storeHavingPackage && $isLastStoreIndex) {
                return $store;
            }
        }

        return null;
    }

    public static function assignOrderIfMatch($order, $callback) 
    {
        /*
         * assign order to store if match  
        */
        if (!is_null($order->lat) && !is_null($order->lng)) {
            $store = (new self)->findStoreForAssign($order);

            if (!is_null($store)) {
                $order->store_id = $store->id;
                $order->save();
            }
        }

        return $callback($order);
    }

    public static function createHistory($order)
    {
        if ($order->store_id == null) return;

        StoreOrderHistory::create([
            'order_id' => $order->id,
            'store_id' => $order->store_id ,
            'status' => $order->status
        ]);

        self::sendNotification($order);
    }

    public static function sendNotification($order)
    {
        $firebaseUtil = new FirebaseUtil;
        $user = null;

        if ($order->store_id != null) {
            $user = $order->store->user;

            if ($user->firebaseTokens != null && strtolower($order->status) == 'pending') {
                $firebaseUtil->sendNewOrderNotification($user->firebaseTokens, $order);
            }
        }
    }
}
