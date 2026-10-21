<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerFaceData;
use App\Models\User;

class FaceRecognitionController extends Controller
{
    /**
     * Finds a matching customer face within the threshold.
     */
    public function matchFace(Request $request)
    {
        $request->validate([
            'embedding' => 'required|array',
            'embedding.*' => 'numeric'
        ]);

        $queryEmbedding = $request->input('embedding');
        $threshold = 0.55; // Euclidean distance threshold (lower is stricter)

        $allFaces = CustomerFaceData::with('customer')->get();

        $bestMatch = null;
        $lowestDistance = $threshold;

        foreach ($allFaces as $faceData) {
            $storedEmbedding = json_decode($faceData->face_embedding, true);

            if (!is_array($storedEmbedding) || count($storedEmbedding) !== count($queryEmbedding)) {
                continue;
            }

            $distance = $this->euclideanDistance($queryEmbedding, $storedEmbedding);

            if ($distance < $lowestDistance) {
                $lowestDistance = $distance;
                $bestMatch = $faceData->customer;
            }
        }

        if ($bestMatch) {
            return response()->json([
                'status' => true,
                'match' => true,
                'customer' => $bestMatch,
                'distance' => $lowestDistance
            ]);
        }

        return response()->json([
            'status' => true,
            'match' => false
        ]);
    }

    /**
     * Stores a face embedding for a customer.
     */
    public function registerFace(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'embedding' => 'required|array',
            'embedding.*' => 'numeric',
            'image_data' => 'nullable|string'
        ]);

        // Don't duplicate if already exists (optional, could update instead)
        $existing = CustomerFaceData::where('customer_id', $request->customer_id)->first();
        if ($existing) {
            $existing->face_embedding = json_encode($request->embedding);
            $existing->save();
        } else {
            CustomerFaceData::create([
                'customer_id' => $request->customer_id,
                'face_embedding' => json_encode($request->embedding)
            ]);
        }

        // Save image if provided
        if ($request->filled('image_data')) {
            $imageData = $request->input('image_data');
            // Check if it is a base64 string
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, etc.
                $imageData = base64_decode($imageData);

                if ($imageData !== false) {
                    $fileName = 'customer/' . uniqid() . '.jpg'; // Using jpg as face-api captures jpeg
                    \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $imageData);

                    $user = User::find($request->customer_id);
                    if ($user) {
                        $user->profile_image = $fileName;
                        $user->save();
                    }
                }
            }
        }

        return response()->json(['status' => true, 'message' => 'Face data registered successfully.']);
    }

    /**
     * Calculate Euclidean Distance between two 128D arrays.
     */
    private function euclideanDistance(array $a, array $b)
    {
        $sum = 0.0;
        foreach ($a as $i => $val) {
            $diff = $val - $b[$i];
            $sum += $diff * $diff;
        }
        return sqrt($sum);
    }
}
