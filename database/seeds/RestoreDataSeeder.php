<?php

use Illuminate\Database\Seeder;

use App\Banner;
use App\Item;
use App\Package;
use App\Promotion;
use App\User;

class RestoreDataSeeder extends Seeder
{
    protected $data;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->restoreUsers();

        $this->restoreItems();

        $this->restoreLaundryPiecesItems();

        $this->restoreLaundryKilosItems();

        $this->restoreLaundryBagItems();

        $this->restorePromotions();

        $this->restoreBanners();
    }

    public function restoreUsers()
    {
        $this->data = json_decode(file_get_contents(__DIR__.'/users.json'), true);
        
        foreach ($this->data as $user) {
            User::create($user);
        }
    }

    public function restoreItems()
    {
        $this->data = [
            [
                "name" => "Shirt",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Polo Shirt",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "T-shirts",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Skirt",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Blouse",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Long Dress",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Gamis",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Muslim Shirt",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Uniform",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Kebaya",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Sajadah",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Bed Cover Big",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Bed Cover Medium",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Bed Cover Small",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Bed Linen Small",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Bed Linen Medium",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Bed Linen Big",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Tussle Bed Linen",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Thick Blanket",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Medium Blanket",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Thin Blanket",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Baby Blanket",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Big Towel",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Medium Towel",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Small Towel",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Pillow Case",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Long Dress",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Batik",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Sajadah",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Hijab",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Pillow",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Jeans",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Long Pants/Skirts",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Short Pants/Skirts",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Gordyn/M2",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Small doll",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Medium Doll",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Big Dolls",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Mukena",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Gloves",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Blazer",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Sweater",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "Per 1 Kg",
                "created_by" => "UltraKlin :: Backup"
            ],
            [
                "name" => "1 Bag 3 Kg",
                "created_by" => "UltraKlin :: Backup"
            ]
        ];

        foreach ($this->data as $item) {
            Item::create($item);
        }
    }

    public function restoreLaundryPiecesItems()
    {
        $this->data = json_decode('
        [
            {
                "item_id": "4",
                "price": "15000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "5",
                "price": "10000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "6",
                "price": "8000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "7",
                "price": "10000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "8",
                "price": "15000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "9",
                "price": "20000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "10",
                "price": "20000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "11",
                "price": "20000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "12",
                "price": "14000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "13",
                "price": "30000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "14",
                "price": "6000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "15",
                "price": "30000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "16",
                "price": "25000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "17",
                "price": "20000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "18",
                "price": "8000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "19",
                "price": "10000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "20",
                "price": "15000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "21",
                "price": "15000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "22",
                "price": "25000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "23",
                "price": "20000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "24",
                "price": "10000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "25",
                "price": "8000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "26",
                "price": "10000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "27",
                "price": "8000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "28",
                "price": "7000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "29",
                "price": "8000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "30",
                "price": "15000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "31",
                "price": "15000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "32",
                "price": "10000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "33",
                "price": "10000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "34",
                "price": "10000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "35",
                "price": "15000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "36",
                "price": "12000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "37",
                "price": "10000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "38",
                "price": "10000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "39",
                "price": "10000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "40",
                "price": "15000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "41",
                "price": "18000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "42",
                "price": "10000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "43",
                "price": "10000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "44",
                "price": "15000",
                "created_by": "UltraKlin :: Backup"
            },
            {
                "item_id": "45",
                "price": "10000",
                "created_by": "UltraKlin :: Backup"
            }
        ]');

        $package = Package::where('name', 'laundry-pieces-regular')->first();

        foreach ($this->data as $item) {
            $package->items()->attach($item->item_id, [
                'price' => $item->price,
                'created_by' => $item->created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function restoreLaundryKilosItems()
    {
        $this->data = json_decode('
            [
                {
                    "item_id": "46",
                    "price": "7000",
                    "created_by": "UltraKlin :: Backup"
                }
            ]
        ');

        $package = Package::where('name', 'laundry-kilos-regular')->first();

        foreach ($this->data as $item) {
            $package->items()->attach($item->item_id, [
                'price' => $item->price,
                'created_by' => $item->created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function restoreLaundryBagItems()
    {
        $this->data = json_decode('
            [
                {
                    "item_id": "47",
                    "price": "21000",
                    "created_by": "UltraKlin :: Backup"
                }
            ]
        ');

        $package = Package::where('name', 'laundry-bag-regular')->first();

        foreach ($this->data as $item) {
            $package->items()->attach($item->item_id, [
                'price' => $item->price,
                'created_by' => $item->created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function restorePromotions()
    {
        $this->data = [
            [
                'id' => '1',
                'package_id' => '1',
                'code' => 'UK50',
                'name' => 'Promo Cleaning 50.000',
                'min_order' => '100000',
                'value' => '50000',
                'day' => ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],
                'time' => ["00:00","23:59"],
                'active' => '1',
                'created_by' => 'UltraKlin :: Backup'
            ],
            [
                'id' => '2',
                'package_id' => '1',
                'code' => 'UK10',
                'name' => 'Promo 10.000 per jam',
                'min_order' => '100000',
                'value' => '80000',
                'day' => ["Mon","Tue","Wed","Thu"],
                'time' => ["09:00","17:00"],
                'target' => 'the-first-2-hours',
                'active' => '1',
                'description' => 'Promo 10.000 per jam, untuk 2 jam pertama',
                'created_by' => 'UltraKlin :: Backup'
            ],
            [
                'id' => '3',
                'package_id' => '3',
                'code' => 'GOCENG',
                'name' => 'Promo Goceng',
                'min_order' => '21000',
                'value' => '2000',
                'day' => ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],
                'time' => ["00:00","23:59"],
                'target' => 'item',
                'active' => '1',
                'description' => 'Promo 5.000 per 1 Kg',
                'created_by' => 'UltraKlin :: Backup'
            ],
            [
                'id' => '4',
                'package_id' => '1',
                'code' => 'AP20',
                'name' => 'Promo 20.000 per jam',
                'min_order' => '100000',
                'value' => '60000',
                'day' => ["Mon","Tue","Wed","Thu"],
                'time' => ["09:00","17:00"],
                'target' => 'the-first-2-hours',
                'active' => '1',
                'description' => 'Promo 20.000 per jam, untuk 2 jam pertama',
                'created_by' => 'UltraKlin :: Backup'
            ],
            [
                'id' => '5',
                'package_id' => '4',
                'code' => 'MEI5',
                'name' => 'Promo Goceng',
                'min_order' => '21000',
                'value' => '6000',
                'day' => ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],
                'time' => ["00:00","23:59"],
                'target' => 'item',
                'active' => '1',
                'description' => 'Promo 5.000 per 1 Kg',
                'created_by' => 'UltraKlin :: Backup'
            ],
        ];

        foreach ($this->data as $item) {
            Promotion::create($item);
        }
    }

    public function restoreBanners() {
        $this->data = [
            [
              'name' => 'Cleaning',
              'description' => 'Promo April AP20',
              'file' => '1525506484P1UFyUr2OONsepSEctGB.png',
              'active' => '1',
              'created_by' => 'UltraKlin :: Backup'
            ],
            [
              'name' => 'Cleaning',
              'description' => 'Promo UK50',
              'file' => '1523770638rinLfcKARtR0EOO2YEpe.png',
              'active' => '1',
              'created_by' => 'UltraKlin :: Backup'
            ],
            [
              'name' => 'Laundry',
              'description' => 'Promo GOCENG',
              'file' => '1523770685AhPMF23pLEAsNqzQhwWB.png',
              'active' => '1',
              'created_by' => 'UltraKlin :: Backup'
            ],
            [
              'name' => 'Laundry',
              'description' => 'Pickup Deliver',
              'file' => '1523770729OhNdISOqQTJNJi3xmv7g.png',
              'active' => '0',
              'created_by' => 'UltraKlin :: Backup'
            ],
            [
              'name' => 'UltraKlin',
              'description' => 'UltraKlin',
              'file' => '1523770754zXIDnhkObfHCkNGYUanB.png',
              'active' => '1',
              'created_by' => 'UltraKlin :: Backup'
            ],
            [
              'name' => 'UltraKlin',
              'description' => 'Nobar',
              'file' => '1524140999SXGp0hf8k4k0lXQwJj5F.png',
              'active' => '0',
              'created_by' => 'UltraKlin :: Backup'
            ]
        ];

        foreach ($this->data as $item) {
            Banner::create($item);
        }
    }
}
