<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Image;

class ImageController extends Controller
{
    protected $request;
    
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getBanners($width, $filename)
    {
        if ($width > 1920) {
            $width = 1920;
        }
        
        $path = storage_path('app/banners/'.$filename);

        $image = Image::cache(function ($image) use ($path, $width) { 
            return $image->make($path)->widen($width);
        });

        return Image::make($image)->response('jpg', 100);
    }

    public function getMenuIcon($width, $filename)
    {
        if ($width > 1920) {
            $width = 1920;
        }

        $path = storage_path('app/images/menu/'.$filename);

        $image = Image::cache(function ($image) use ($path, $width) { 
            return $image->make($path)->widen($width);
        });

        return Image::make($image)->response('png', 100);
    }

    public function getStoreImage($width, $filename)
    {
        if ($width > 1920) {
            $width = 1920;
        }

        $path = storage_path('app/images/store/'.$filename);

        $image = Image::cache(function ($image) use ($path, $width) { 
            return $image->make($path)->widen($width);
        });

        return Image::make($image)->response('jpg', 100);
    }
}
