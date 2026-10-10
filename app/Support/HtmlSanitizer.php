<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Pembersih HTML berbasis allowlist untuk konten dari editor (CKEditor).
 *
 * - Tag berbahaya (script, style, form, object, ...) dibuang beserta isinya.
 * - Tag tidak dikenal "dibuka" (isinya dipertahankan, tag-nya dibuang).
 * - Hanya atribut yang diizinkan yang dipertahankan; event handler (on*) selalu dibuang.
 * - URL hanya boleh http(s), mailto, tel, relatif, atau data:image untuk gambar.
 */
class HtmlSanitizer
{
    private const REMOVE_WITH_CONTENT = [
        'script', 'style', 'noscript', 'template', 'object', 'embed', 'applet', 'form', 'input',
        'button', 'select', 'textarea', 'option', 'link', 'meta', 'base', 'svg', 'math', 'frame', 'frameset',
    ];

    private const ALLOWED = [
        'p' => [], 'br' => [], 'hr' => [], 'span' => [], 'div' => [],
        'h1' => [], 'h2' => [], 'h3' => [], 'h4' => [], 'h5' => [], 'h6' => [],
        'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [], 'strike' => [],
        'sub' => [], 'sup' => [], 'mark' => [], 'small' => [], 'blockquote' => [], 'pre' => [], 'code' => [],
        'ul' => [], 'ol' => ['start', 'type', 'reversed'], 'li' => [],
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'loading'],
        'figure' => [], 'figcaption' => [],
        'table' => [], 'thead' => [], 'tbody' => [], 'tfoot' => [], 'tr' => [], 'caption' => [],
        'th' => ['colspan', 'rowspan', 'scope'], 'td' => ['colspan', 'rowspan'], 'colgroup' => [], 'col' => ['span'],
        'oembed' => ['url'],
        'iframe' => ['src', 'width', 'height', 'title', 'allowfullscreen', 'frameborder', 'allow'],
    ];

    private const GLOBAL_ATTRIBUTES = ['class', 'style', 'id', 'dir', 'lang'];

    private const URL_ATTRIBUTES = ['href', 'src', 'url'];

    // Embed video/peta yang diizinkan
    private const IFRAME_HOSTS = [
        'www.youtube.com', 'youtube.com', 'www.youtube-nocookie.com', 'player.vimeo.com',
        'www.google.com', 'maps.google.com',
    ];

    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="__sanitizer_root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('__sanitizer_root');
        if (!$root) {
            return e(strip_tags($html));
        }

        self::cleanChildren($root);

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return $output;
    }

    private static function cleanChildren(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE || $child->nodeType === XML_PI_NODE) {
                $node->removeChild($child);
                continue;
            }

            if (!$child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);

            if (in_array($tag, self::REMOVE_WITH_CONTENT, true)) {
                $node->removeChild($child);
                continue;
            }

            if (!array_key_exists($tag, self::ALLOWED)) {
                // buka tag tak dikenal, pertahankan isinya
                self::cleanChildren($child);
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            if ($tag === 'iframe' && !self::isAllowedIframe($child->getAttribute('src'))) {
                $node->removeChild($child);
                continue;
            }

            self::cleanAttributes($child, $tag);
            self::cleanChildren($child);
        }
    }

    private static function cleanAttributes(DOMElement $element, string $tag): void
    {
        $allowed = array_merge(self::GLOBAL_ATTRIBUTES, self::ALLOWED[$tag]);

        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->name);
            $value = $attribute->value;

            $keep = in_array($name, $allowed, true)
                && (!in_array($name, self::URL_ATTRIBUTES, true) || self::isSafeUrl($value, $tag === 'img' && $name === 'src'))
                && ($name !== 'style' || self::isSafeStyle($value));

            if (!$keep) {
                $element->removeAttribute($attribute->name);
            }
        }

        if ($tag === 'a' && strtolower($element->getAttribute('target')) === '_blank') {
            $element->setAttribute('rel', 'noopener noreferrer');
        }
    }

    public static function isSafeUrl(string $url, bool $allowDataImage = false): bool
    {
        // hilangkan spasi & karakter kontrol yang bisa dipakai untuk menyamarkan skema
        $normalized = strtolower(preg_replace('/[\x00-\x20\x7F]+/', '', html_entity_decode($url, ENT_QUOTES | ENT_HTML5)));

        if ($normalized === '' || str_starts_with($normalized, '#') || str_starts_with($normalized, '/') || str_starts_with($normalized, '?')) {
            return true;
        }

        if ($allowDataImage && preg_match('#^data:image/(png|jpe?g|gif|webp);base64,#', $normalized)) {
            return true;
        }

        if (!preg_match('/^([a-z][a-z0-9+.\-]*):/', $normalized, $match)) {
            return true; // relatif, mis. "gambar.jpg"
        }

        return in_array($match[1], ['http', 'https', 'mailto', 'tel'], true);
    }

    private static function isSafeStyle(string $style): bool
    {
        $normalized = strtolower(preg_replace('/\s+/', '', $style));

        return !preg_match('/expression\(|javascript:|vbscript:|url\(|@import|behavior:|-moz-binding/', $normalized);
    }

    private static function isAllowedIframe(string $src): bool
    {
        if (!preg_match('#^https://#i', trim($src))) {
            return false;
        }

        return in_array(strtolower((string) parse_url($src, PHP_URL_HOST)), self::IFRAME_HOSTS, true);
    }
}
