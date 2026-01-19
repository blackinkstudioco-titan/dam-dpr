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