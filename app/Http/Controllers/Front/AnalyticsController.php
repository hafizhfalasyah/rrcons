<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Support\Analytics\Tracker;
use Illuminate\Http\Request;

/**
 * Endpoint yang dipanggil browser (navigator.sendBeacon / fetch keepalive).
 * Body berupa JSON dengan Content-Type text/plain agar tidak memicu preflight,
 * jadi dibaca manual. Selalu membalas 204 supaya tidak membocorkan alasan penolakan.
 */
class AnalyticsController extends Controller
{
    public function hit(Request $request)
    {
        Tracker::recordHit($request, $this->body($request));

        return response()->noContent();
    }

    public function ping(Request $request)
    {
        Tracker::recordPing($request, $this->body($request));

        return response()->noContent();
    }

    private function body(Request $request): array
    {
        $raw = $request->getContent();

        if ($raw === '' || strlen($raw) > 4096) {
            return [];
        }

        $data = json_decode($raw, true);

        return is_array($data) ? $data : [];
    }
}
