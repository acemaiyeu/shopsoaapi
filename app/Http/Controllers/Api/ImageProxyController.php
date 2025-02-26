<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ImageProxyController extends Controller
{
    //
    public function fetchImage(Request $request)
    {
        $url = $request->query('url');

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'Invalid URL'], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Gửi request đến Imgur
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0', // Bypass 403
                'Referer' => 'https://imgur.com/'
            ])->get($url);

            if (!$response->successful()) {
                return response()->json(['error' => 'Cannot fetch image'], $response->status());
            }

            // Lấy nội dung ảnh
            $content = $response->body();
            $mimeType = $response->header('Content-Type', 'image/jpeg'); // Mặc định là JPEG

            return response($content, 200)->header('Content-Type', $mimeType);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
