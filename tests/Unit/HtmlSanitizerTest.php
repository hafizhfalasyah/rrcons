<?php

namespace Tests\Unit;

use App\Support\HtmlSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_it_keeps_editor_formatting(): void
    {
        $html = '<h2>Judul</h2><p>Teks <strong>tebal</strong>, <em>miring</em> é ✓</p>'
            . '<ul><li>satu</li></ul><blockquote>kutipan</blockquote>'
            . '<figure class="image"><img src="/storage/a.jpg" alt="a"><figcaption>cap</figcaption></figure>'
            . '<table><tr><td colspan="2">sel</td></tr></table>'
            . '<p style="text-align:center"><a href="https://example.com" target="_blank">tautan</a></p>';

        $clean = HtmlSanitizer::clean($html);

        $this->assertStringContainsString('<h2>Judul</h2>', $clean);
        $this->assertStringContainsString('<strong>tebal</strong>', $clean);
        $this->assertStringContainsString('é ✓', $clean);
        $this->assertStringContainsString('<img src="/storage/a.jpg" alt="a">', $clean);
        $this->assertStringContainsString('colspan="2"', $clean);
        $this->assertStringContainsString('style="text-align:center"', $clean);
        $this->assertStringContainsString('rel="noopener noreferrer"', $clean);
    }

    public function test_it_removes_scripts_and_dangerous_attributes(): void
    {
        $clean = HtmlSanitizer::clean(
            '<script>alert(1)</script><style>body{}</style>'
            . '<img src="x" onerror="alert(1)">'
            . '<a href="javascript:alert(1)">a</a>'
            . '<a href=" jav&#x09;ascript:alert(1)">b</a>'
            . '<a href="data:text/html;base64,xxx">c</a>'
            . '<p style="background:url(javascript:alert(1))">d</p>'
            . '<svg onload="alert(1)"><circle/></svg>'
            . '<iframe src="https://evil.example"></iframe>'
            . '<form action="/x"><input name="a"></form>'
            . '<!-- komentar -->'
        );

        foreach (['<script', '<style', 'onerror', 'javascript:', 'data:text', 'url(', '<svg', 'onload', 'evil.example', '<form', '<input', '<!--'] as $needle) {
            $this->assertStringNotContainsString($needle, $clean);
        }
    }

    public function test_it_unwraps_unknown_tags_and_allows_trusted_embeds(): void
    {
        $clean = HtmlSanitizer::clean('<font color="red">teks</font><iframe src="https://www.youtube.com/embed/abc"></iframe>');

        $this->assertStringContainsString('teks', $clean);
        $this->assertStringNotContainsString('<font', $clean);
        $this->assertStringContainsString('<iframe src="https://www.youtube.com/embed/abc"></iframe>', $clean);
    }

    public function test_empty_input_returns_empty_string(): void
    {
        $this->assertSame('', HtmlSanitizer::clean(null));
        $this->assertSame('', HtmlSanitizer::clean('   '));
    }
}
