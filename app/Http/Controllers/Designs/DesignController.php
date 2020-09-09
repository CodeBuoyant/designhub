<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use App\Repositories\Contracts\IDesign;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\ForUser;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DesignController extends Controller
{
    protected $designs;

    public function __construct(IDesign $designs) {
        $this->designs = $designs;
    }

    public function index() {
        $designs = $this->designs->withCriteria([
            new LatestFirst(),
            new IsLive(),
            new ForUser(1),
            new EagerLoad(['user', 'comments'])
        ])->all();
        return DesignResource::collection($designs);
    }

    public function findDesign($id) {
        $design = $this->designs->find($id);
        return new DesignResource($design);
    }

    public function update(Request $request, $id) {
        $design = $this->designs->find($id);

        $this->authorize('update', $design);

        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,' . $id],
            'description' => ['required', 'string', 'min:20', 'max:140'],
            'tags' => ['required']
        ]);

        $design = $this->designs->update($id, [
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => ! $design->upload_successful ? false : $request->is_live
        ]);

        // Apply the tags
        $this->designs->applyTags($id, $request->tags);

        return new DesignResource($design);
    }

    public function destroy($id) {
        $design = $this->designs->find($id);

        $this->authorize('delete', $design);

        // Delete files associated with the record
        foreach (['thumbnail', 'large', 'original'] as $size) {
            // Check if the file exists
            if ( Storage::disk($design->disk)->exists("uploads/designs/{$size}/" . $design->image) ) {
                Storage::disk($design->disk)->delete("uploads/designs/{$size}/" . $design->image);
            }
        }

        $this->designs->delete($id);

        return response()->json(['message' => 'Record deleted'], 200);
    }

    public function like($id) {
        $this->designs->like($id);

        return response()->json(['message' => 'Successful'], 200);
    }

    public function checkIfUserHasLiked($designId) {
        $isLiked= $this->designs->isLikedByUser($designId);

        return response()->json(['liked' => $isLiked], 200);
    }
}
