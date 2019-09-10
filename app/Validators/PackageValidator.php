<?php

namespace App\Validators;

use App\V1\Package;

class PackageValidator {
    public function validatePackageInRegion($attribute, $value, $parameters, $validator)
    {
        $package = Package::where('region_id', $parameters[0])
            ->where('name', $value)
            ->first();

        if (is_null($package)) return false;

        return true;
    }

    public function messagePackageInRegion($message, $attribute, $rule, $parameters) {
        return 'Package not exists on selected region.';
    }
}