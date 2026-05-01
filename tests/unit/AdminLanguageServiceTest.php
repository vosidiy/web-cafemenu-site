<?php

use App\Services\AdminLanguageService;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Session\Session;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class AdminLanguageServiceTest extends CIUnitTestCase
{
    public function testResolveCurrentLanguageCodePrefersSessionOverCookieAndBrowser(): void
    {
        $service = new AdminLanguageService();
        $request = $this->createRequestMock('uz', 'ru-RU,ru;q=0.9,en-US;q=0.8');
        $session = $this->createMock(Session::class);

        $session->expects($this->once())
            ->method('get')
            ->with('admin_language')
            ->willReturn('ru');
        $session->expects($this->never())->method('set');

        $this->assertSame('ru', $service->resolveCurrentLanguageCode($request, $session));
    }

    public function testResolveCurrentLanguageCodeFallsBackToCookieBeforeBrowser(): void
    {
        $service = new AdminLanguageService();
        $request = $this->createRequestMock('uz', 'ru-RU,ru;q=0.9,en-US;q=0.8');
        $session = $this->createMock(Session::class);

        $session->method('get')->with('admin_language')->willReturn('');
        $session->expects($this->once())
            ->method('set')
            ->with('admin_language', 'uz');

        $this->assertSame('uz', $service->resolveCurrentLanguageCode($request, $session));
    }

    public function testResolveCurrentLanguageCodeFallsBackToBrowserThenEnglish(): void
    {
        $service = new AdminLanguageService();
        $request = $this->createRequestMock('', 'ru-RU,ru;q=0.9,en-US;q=0.8');
        $session = $this->createMock(Session::class);

        $session->method('get')->with('admin_language')->willReturn('');
        $session->expects($this->once())
            ->method('set')
            ->with('admin_language', 'ru');

        $this->assertSame('ru', $service->resolveCurrentLanguageCode($request, $session));
    }

    public function testResolveCurrentLanguageCodeUsesEnglishWhenNothingValidIsStored(): void
    {
        $service = new AdminLanguageService();
        $request = $this->createRequestMock('xx', 'xx-XX,xx;q=0.9');
        $session = $this->createMock(Session::class);

        $session->method('get')->with('admin_language')->willReturn('zz');
        $session->expects($this->once())
            ->method('set')
            ->with('admin_language', 'en');

        $this->assertSame('en', $service->resolveCurrentLanguageCode($request, $session));
    }

    public function testPersistLanguageWritesSessionAndCookie(): void
    {
        $service = new AdminLanguageService();
        $response = $this->createMock(ResponseInterface::class);
        $session = $this->createMock(Session::class);

        $session->expects($this->once())
            ->method('set')
            ->with('admin_language', 'ru');

        $response->expects($this->once())
            ->method('setCookie')
            ->with(
                'admin_language',
                'ru',
                $this->isType('int'),
                '',
                '/',
                '',
                null,
                true,
                'Lax',
            )
            ->willReturnSelf();

        $this->assertSame('ru', $service->persistLanguage('ru', $response, $session));
    }

    public function testSanitizeRedirectTargetAllowsOnlyInternalPaths(): void
    {
        $service = new AdminLanguageService();

        $this->assertSame('admin/settings?tab=languages', $service->sanitizeRedirectTarget('/admin/settings?tab=languages', 'login'));
        $this->assertSame('login', $service->sanitizeRedirectTarget('https://example.com/admin', 'login'));
        $this->assertSame('admin', $service->sanitizeRedirectTarget('//evil.test/path', 'admin'));
        $this->assertSame('login', $service->sanitizeRedirectTarget('', 'login'));
    }

    private function createRequestMock(string $cookieValue, string $acceptLanguage): IncomingRequest
    {
        $request = $this->getMockBuilder(IncomingRequest::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getCookie', 'getHeaderLine'])
            ->getMock();

        $request->method('getCookie')
            ->with('admin_language')
            ->willReturn($cookieValue);

        $request->method('getHeaderLine')
            ->with('Accept-Language')
            ->willReturn($acceptLanguage);

        return $request;
    }
}
