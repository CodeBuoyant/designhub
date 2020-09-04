<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Jobs\UploadImage;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request) {
        // Validate the request
        $this->validate($request, [
            'image' => ['required', 'mimes:jpeg,jpg,gif,bmp,png', 'max:2048']
        ]);

        // Get image from request
        $image = $request->file('image');
        $image_path = $image->getPathName();

        // Get the original file name and replace any spaces with _ and change to lower cases
        $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));

        // Move image to temporary location
        $tmp = $image->storeAs('uploads/original', $filename, 'tmp');

        // Create database record for the design
        $design = auth()->user()->designs()->create([
            'image' => $filename,
            'disk' => config('site.upload_disk')
        ]);

        // Dispatch a job to handle the image manipulation
        $this->dispatch(new UploadImage($design));

        return response()->json($design, 200);
    }
}
