<?php

use Illuminate\Database\Seeder;

use App\Item;
use App\Package;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createLaundryRegular();
    }

    public function createLaundryRegular()
    {
        $package = Package::where('name', 'cleaning-regular')->first();

        $items = Item::whereIn('id', [1, 2, 3])
            ->get();

       foreach ($items as $item) {
           $package->items()->attach($item->id, [
               'price' => 25000,
               'created_at' => date('Y-m-d H:i:s'),
               'updated_at' => date('Y-m-d H:i:s'),
           ]);
       }
    }
}
