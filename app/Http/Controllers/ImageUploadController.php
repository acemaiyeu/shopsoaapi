<?php

namespace App\Http\Controllers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ImageUploadController extends Controller
{
    // public function upload(Request $request)
    // {
    //     $request->validate([
    //         'image' => 'required|image|max:5120',  // max 5MB
    //     ]);

    //     $image = $request->file('image');

    //     if (!$image || !$image->isValid()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'File không hợp lệ hoặc lỗi khi upload.',
    //         ], 400);
    //     }

    //     $imageData = base64_encode($image->get());

    //     $apiKey = '229ee60d9e97cbbf126b14c4686769dc';

    //     $response = Http::asForm()->post('https://api.imgbb.com/1/upload', [
    //         'key' => $apiKey,
    //         'image' => $imageData,
    //     ]);

    //     if ($response->successful()) {
    //         return response()->json([
    //             'success' => true,
    //             'url' => $response['data']['url']
    //         ]);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Upload thất bại.',
    //             'error' => $response->json()
    //         ], 500);
    //     }
    // }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',  // max 5MB
        ]);

        $imagePath = $request->file('image')->getPathname();

        $client = new Client();

        try {
            $response = $client->post('https://api.cloudinary.com/v1_1/dvxiodcxu/image/upload', [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($imagePath, 'r'),
                    ],
                    [
                        'name' => 'upload_preset',
                        'contents' => 'ml_default',
                    ],
                ],
            ]);

            $body = json_decode($response->getBody(), true);

            return response()->json([
                'success' => true,
                'url' => $body['secure_url'] ?? null,
                'data' => $body,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload thất bại: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function testUpload(Request $request)
    {
        $file = $request->file('image');

        $path = $file->getPathname();

        $curl = curl_init();

        $cloudName = 'dvxiodcxu';
        $uploadPreset = 'ml_default';

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'file' => new \CURLFile($path),
                'upload_preset' => $uploadPreset
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return response()->json(json_decode($response, true));
    }
}
