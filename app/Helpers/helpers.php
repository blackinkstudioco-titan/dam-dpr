<?php
/**
 * FILE: app/Helpers/helpers.php
 * 
 * VERSI SIMPLE - HANYA REGEX, TIDAK BUTUH PHP XML EXTENSION
 * 
 * CARA INSTALL:
 * 1. Buat file ini di app/Helpers/helpers.php
 * 2. Edit composer.json:
 *    "autoload": {
 *        "files": ["app/Helpers/helpers.php"]
 *    }
 * 3. Run: composer dump-autoload
 */

if (!function_exists('add_image_caption')) {
    /**
     * Menambahkan figcaption pada semua img yang memiliki alt
     * Menggunakan regex - simple dan cepat
     * 
     * @param string|null $html
     * @return string
     */
    function add_image_caption(?string $html): string
    {
        if (empty($html)) {
            return '';
        }
        
        // Pattern untuk menangkap img dengan alt
        $pattern = '/<img([^>]*?)alt=["\']([^"\']+)["\']([^>]*?)>/i';
        
        $result = preg_replace_callback($pattern, function($matches) {
            $imgTag = $matches[0];
            $altText = trim($matches[2]);
            
            // Skip jika alt kosong
            if (empty($altText)) {
                return $imgTag;
            }
            
            // Escape HTML entities untuk keamanan
            $captionText = htmlspecialchars($altText, ENT_QUOTES, 'UTF-8');
            
            // Buat struktur figure dengan figcaption
            $output = '<figure class="pb-3">';
            $output .= $imgTag;
            $output .= '<figcaption class="text-sm text-gray-600 italic mt-2 text-left">';
            $output .= $captionText;
            $output .= '/Dok.DPR RI';
            $output .= '</figcaption>';
            $output .= '</figure>';
            
            return $output;
        }, $html);
        
        return $result;
    }
}

if (!function_exists('remove_image_caption')) {
    /**
     * Menghapus semua figcaption dari HTML
     * Berguna untuk editing
     * 
     * @param string|null $html
     * @return string
     */
    function remove_image_caption(?string $html): string
    {
        if (empty($html)) {
            return '';
        }
        
        // Remove figure tag tapi keep img
        $html = preg_replace('/<figure[^>]*>/i', '', $html);
        $html = preg_replace('/<\/figure>/i', '', $html);
        
        // Remove figcaption
        $html = preg_replace('/<figcaption[^>]*>.*?<\/figcaption>/is', '', $html);
        
        return $html;
    }
}

if (!function_exists('sanitize_rich_text')) {
    /**
     * SECURITY FIX: article/photo rich-text fields (TinyMCE "isi") were
     * being saved as-is and echoed back with {!! !!} (raw, unescaped HTML)
     * on both the admin editor pages and the public front-end. That's a
     * stored-XSS hole: anything that ends up in that field — a pasted
     * <script> tag, an <img onerror=...>, a javascript: link, etc. — would
     * execute in the browser of every visitor (and every other logged-in
     * staff member) who views the article.
     *
     * This strips genuinely dangerous constructs (script/style/iframe/
     * object/embed/form tags, inline event handlers, javascript:/vbscript:
     * URLs) while leaving normal rich-text formatting (bold, links, images,
     * tables, etc.) untouched, so existing articles keep rendering the
     * same way.
     *
     * @param string|null $html
     * @return string
     */
    function sanitize_rich_text(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $wrapped = '<?xml encoding="UTF-8"><div id="__sanitize_root__">' . $html . '</div>';
        $dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $root = $dom->getElementById('__sanitize_root__');
        if (!$root) {
            // Parsing failed entirely; fail safe by stripping all tags.
            return htmlspecialchars(strip_tags($html), ENT_QUOTES, 'UTF-8');
        }

        $disallowedTags = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'link', 'meta', 'base'];
        foreach ($disallowedTags as $tag) {
            $nodes = $root->getElementsByTagName($tag);
            for ($i = $nodes->length - 1; $i >= 0; $i--) {
                $node = $nodes->item($i);
                $node->parentNode->removeChild($node);
            }
        }

        $xpath = new DOMXPath($dom);
        $allNodes = $xpath->query('.//*', $root);

        foreach ($allNodes as $node) {
            if (!($node instanceof DOMElement)) {
                continue;
            }

            $attributesToRemove = [];
            foreach ($node->attributes as $attr) {
                $name = strtolower($attr->name);
                $value = trim($attr->value);

                // Strip all inline event handlers (onclick, onerror, ...).
                if (str_starts_with($name, 'on')) {
                    $attributesToRemove[] = $attr->name;
                    continue;
                }

                // Strip javascript:/vbscript:/data: URLs in URL-bearing attrs.
                if (in_array($name, ['href', 'src', 'action', 'formaction'])) {
                    $normalized = strtolower(preg_replace('/\s+/', '', $value));
                    if (preg_match('/^(javascript|vbscript|data):/i', $normalized)) {
                        $attributesToRemove[] = $attr->name;
                    }
                }

                if ($name === 'style' && preg_match('/expression\s*\(|javascript:/i', $value)) {
                    $attributesToRemove[] = $attr->name;
                }
            }

            foreach ($attributesToRemove as $attrName) {
                $node->removeAttribute($attrName);
            }
        }

        $result = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $result .= $dom->saveHTML($child);
        }

        return $result;
    }
}

if (!function_exists('excel_safe')) {
    /**
     * SECURITY FIX: Excel/CSV formula injection. Report exports embed
     * user-supplied text (article/photo titles, descriptions, keywords)
     * straight into spreadsheet cells. If that text starts with
     * =, +, -, or @, Excel/LibreOffice will treat it as a formula when the
     * file is opened, which can be abused (e.g. to shell out via legacy
     * DDE, or to exfiltrate data via a formula calling out to a URL).
     * Prefixing a leading apostrophe forces the cell to be treated as
     * plain text.
     *
     * @param mixed $value
     * @return mixed
     */
    function excel_safe($value)
    {
        if (!is_string($value) || $value === '') {
            return $value;
        }

        if (preg_match('/^[=+\-@\t\r]/', $value)) {
            return "'" . $value;
        }

        return $value;
    }
}

if (!function_exists('get_first_image_caption')) {
    /**
     * Mengambil caption dari gambar pertama
     * Berguna untuk meta description atau preview
     * 
     * @param string|null $html
     * @return string|null
     */
    function get_first_image_caption(?string $html): ?string
    {
        if (empty($html)) {
            return null;
        }
        
        // Cari img pertama yang punya alt
        preg_match('/<img[^>]*alt=["\']([^"\']+)["\'][^>]*>/i', $html, $matches);
        
        return $matches[1] ?? null;
    }
}