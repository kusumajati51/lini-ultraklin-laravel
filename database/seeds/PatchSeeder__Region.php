<?php

use Illuminate\Database\Seeder;

use App\Officer;
use App\Region;

class PatchSeeder__Region extends Seeder
{
    protected $region;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = [
            [
                'code' => 'JKT',
                'name' => 'Jakarta'       
            ],
            [
                'code' => 'DPK',
                'name' => 'Depok'       
            ],
            [
                'code' => 'TGT',
                'name' => 'Tanggerang'       
            ],
            [
                'code' => 'BKS',
                'name' => 'Bekasi'       
            ],
            [
                'code' => 'SBY',
                'name' => 'Surabaya'       
            ]
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }

        $this->region = Region::find(1);

        $this->assignToRegion();
    }

    public function assignToRegion()
    {
        DB::table('packages')->update([
            'region_id' => $this->region->id
        ]);

        DB::table('promotions')->update([
            'region_id' => $this->region->id
        ]);

        DB::table('customers')->update([
            'region_id' => $this->region->id
        ]);

        DB::table('orders')->update([
            'region_id' => $this->region->id
        ]);

        $officers = Officer::all();

        foreach ($officers as $officer) {
            $officer->regions()->attach($this->region->id);
        }
    }
}
