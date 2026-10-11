<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Tests\TestCase;

class CsrfProtectionTest extends TestCase
{
    /**
     * Middleware CSRF aplikasi, tanpa pengecualian otomatis saat unit test.
     */
    private function middleware(): VerifyCsrfToken
    {
        return new class(app(), app('encrypter')) extends VerifyCsrfToken {
            protected function runningUnitTests()
            {
                return false;
            }
        };
    }

    private function request(string $uri, array $input = [], array $server = []): Request
    {
        $request = Request::create($uri, 'POST', $input, [], [], $server);
        $session = app('session')->driver();
        $session->put('_token', 'valid-token');
        $request->setLaravelSession($session);

        return $request;
    }

    public function test_same_origin_request_without_token_is_still_rejected(): void
    {
        // Laravel 13 meloloskan Sec-Fetch-Site: same-origin; aplikasi tetap mewajibkan token
        $this->expectException(TokenMismatchException::class);

        $this->middleware()->handle(
            $this->request('/blog/artikel/comment', [], ['HTTP_SEC_FETCH_SITE' => 'same-origin']),
            fn () => response('ok')
        );
    }

    public function test_request_with_valid_token_passes(): void
    {
        $response = $this->middleware()->handle(
            $this->request('/blog/artikel/comment', ['_token' => 'valid-token']),
            fn () => response('ok')
        );

        $this->assertSame('ok', $response->getContent());
    }

    public function test_analytics_beacons_stay_excluded(): void
    {
        $response = $this->middleware()->handle($this->request('/_v/hit'), fn () => response('ok'));

        $this->assertSame('ok', $response->getContent());
    }
}
