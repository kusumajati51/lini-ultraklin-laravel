<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Region;
use App\Package;

class CreateRegionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ultraklin:create-region';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create UltraKlin region';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $code = $this->ask('Enter region code');
        $name = $this->ask('Enter region display name');
        $copy = $this->choice('Do you want copy packages and items from other region?', ['No', "Yes"], 'Yes');

        if ($copy == 'Yes') {
            $regions = Region::select('code', 'name')->orderBy('name')->get();

            $regions = $regions->map(function ($region) {
                return $region->code.' :: '.$region->name;
            })->toArray();

            $selectedRegion = $this->choice('Select region for copy packages and items', $regions);
            $selectedRegionArr = explode(' :: ', $selectedRegion);
            $selectedRegionCode = $selectedRegionArr[0];
            $selectedRegionName = $selectedRegionArr[1];

            $region = Region::select('id')->where('code', $selectedRegionCode)->first();

            $copyPackages = $region->packages()->select(
                'id', 'service_id', 'name', 'display_name', 'description'
            )->get();

            $packages = $copyPackages->map(function ($copyPackage) use ($selectedRegionName, $name) {
                $package = (object) [
                    'service_id' => $copyPackage->service_id,
                    'name' => str_slug(preg_replace("/{$selectedRegionName}/i", strtolower($name), $copyPackage->name)),
                    'display_name' => preg_replace("/{$selectedRegionName}/i", ucwords($name), $copyPackage->display_name),
                    'items' => $copyPackage->items->map(function ($item) {
                        return (object) [
                            'id' => $item->id,
                            'price' => $item->pivot->price
                        ];
                    })
                ];

                return $package;
            });

            $region = new Region;
            $region->code = strtoupper($code);
            $region->name = ucwords($name);
            $region->save();

            foreach ($packages as $package) {
                $regionPackage = new Package;
                $regionPackage->region_id = $region->id;
                $regionPackage->service_id = $package->service_id;
                $regionPackage->name = $package->name;
                $regionPackage->display_name = $package->display_name;
                $regionPackage->save();

                foreach ($package->items as $item) {
                    $regionPackage->items()->attach($item->id, [
                        'price' => $item->price
                    ]);
                }
            }

            $this->info('New region has created.');
        }
    }
}
