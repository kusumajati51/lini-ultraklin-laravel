<?php

use Illuminate\Database\Seeder;

use App\Service;
use App\Package;
use App\Item;

class ServiceDataSeeder extends Seeder
{
    protected $services;

    protected $packages;

    protected $items;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->prepareServices();
        $this->storeServices();

        $this->preparePackages();
        $this->storePackages();

        $this->prepareItems();
        $this->storeItems();
    }

    public function prepareServices()
    {
        $this->services = [
            [
                'parent_id' => null,
                'name' => 'cleaning',
                'display_name' => 'Cleaning'
            ],
            [
                'parent_id' => null,
                'name' => 'laundry',
                'display_name' => 'Laundry'
            ],
            [
                'parent_id' => 2,
                'name' => 'laundry-pieces',
                'display_name' => 'Laundry Pieces'
            ],
            [
                'parent_id' => 2,
                'name' => 'laundry-kilos',
                'display_name' => 'Laundry Kilos'
            ],
        ];
    }

    public function storeServices()
    {
        foreach ($this->services as $service) {
            $_service = new Service;
            $_service->parent_id = $service['parent_id'];
            $_service->name = $service['name'];
            $_service->display_name = $service['display_name'];
            $_service->save();
        }
    }

    public function preparePackages()
    {
        $this->packages = [
            [
                'service_id' => 1,
                'name' => 'cleaning-regular',
                'display_name' => 'Cleaning Regular'
            ],
            [
                'service_id' => 3,
                'name' => 'laundry-pieces-regular',
                'display_name' => 'Laundry Pieces Regular'
            ],
            [
                'service_id' => 4,
                'name' => 'laundry-kilos-regular',
                'display_name' => 'Laundry Kilos Regular'
            ],
            [
                'service_id' => 4,
                'name' => 'laundry-bag-regular',
                'display_name' => 'Laundry Bag Regular'
            ]
        ];
    }

    public function storePackages()
    {
        foreach ($this->packages as $package) {
            $_package = new Package;
            $_package->service_id = $package['service_id'];
            $_package->name = $package['name'];
            $_package->display_name = $package['display_name'];
            $_package->save();
        }
    }

    public function prepareItems()
    {
        $this->items = [
            [
                'name' => 'Bathroom',
            ],
            [
                'name' => 'Bedroom',
            ],
            [
                'name' => 'Others',
            ]
        ];
    }

    public function storeItems()
    {
        foreach ($this->items as $item) {
            $_item = new Item;
            $_item->name = $item['name'];
            $_item->save();
        }
    }
}
