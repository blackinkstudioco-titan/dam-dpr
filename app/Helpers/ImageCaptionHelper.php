<?php
// app/Helpers/ImageCaptionHelper.php

namespace App\Helpers;

use DOMDocument;
use DOMXPath;

class ImageCaptionHelper
{
    /**
     * Menambahkan figcaption pada semua img yang memiliki alt
     * 
     * @param string $html
     * @return string
     */
    public static function addFigcaption(string $html): string
    {
        if (empty($html)) {
            return $html;
        }

        // Nonaktifkan error untuk HTML yang tidak sempurna
        libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        
        // Load HTML dengan UTF-8 encoding
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        // Remove encoding tag yang ditambahkan
        foreach ($dom->childNodes as $item) {
            if ($item->nodeType == XML_PI_NODE) {
                $dom->removeChild($item);
            }
        }
        
        $xpath = new DOMXPath($dom);
        
        // Cari semua img tag
        $images = $xpath->query('//img[@alt]');
        
        foreach ($images as $img) {
            $altText = $img->getAttribute('alt');
            
            // Skip jika alt kosong
            if (empty(trim($altText))) {
                continue;
            }
            
            // Cek apakah img sudah dibungkus figure
            $parent = $img->parentNode;
            if ($parent->nodeName === 'figure') {
                // Sudah ada figure, cek apakah ada figcaption
                $existingCaption = $xpath->query('./figcaption', $parent);
                if ($existingCaption->length === 0) {
                    // Tambahkan figcaption
                    $figcaption = $dom->createElement('figcaption', htmlspecialchars($altText));
                    $parent->appendChild($figcaption);
                }
                continue;
            }
            
            // Buat element figure
            $figure = $dom->createElement('figure');
            
            // Copy semua class dari parent div ke figure (jika ada)
            if ($parent->nodeName === 'div' && $parent->hasAttribute('class')) {
                $figure->setAttribute('class', $parent->getAttribute('class'));
            }
            
            // Clone img element
            $newImg = $img->cloneNode(true);
            
            // Buat figcaption
            $figcaption = $dom->createElement('figcaption', htmlspecialchars($altText));
            $figcaption->setAttribute('class', 'text-sm text-gray-600 italic mt-2 text-center');
            
            // Susun struktur: figure > img + figcaption
            $figure->appendChild($newImg);
            $figure->appendChild($figcaption);
            
            // Replace img dengan figure di parent
            $parent->replaceChild($figure, $img);
        }
        
        // Clear error buffer
        libxml_clear_errors();
        
        // Kembalikan HTML
        return $dom->saveHTML();
    }
    
    /**
     * Versi alternatif menggunakan regex (lebih cepat tapi kurang robust)
     * 
     * @param string $html
     * @return string
     */
    public static function addFigcaptionRegex(string $html): string
    {
        if (empty($html)) {
            return $html;
        }
        
        // Pattern untuk menangkap img dengan alt
        $pattern = '/<img([^>]*?)alt=["\']([^"\']+)["\']([^>]*?)>/i';
        
        return preg_replace_callback($pattern, function($matches) {
            $imgTag = $matches[0];
            $altText = $matches[2];
            
            // Skip jika alt kosong
            if (empty(trim($altText))) {
                return $imgTag;
            }
            
            // Escape HTML entities untuk figcaption
            $captionText = htmlspecialchars($altText);
            
            // Buat struktur figure
            $figure = '<figure>';
            $figure .= $imgTag;
            $figure .= '<figcaption class="text-sm text-gray-600 italic mt-2 text-center">' . $captionText . '</figcaption>';
            $figure .= '</figure>';
            
            return $figure;
        }, $html);
    }
}

// Daftarkan helper function global
if (!function_exists('add_image_caption')) {
    /**
     * Helper function untuk menambahkan caption pada gambar
     * 
     * @param string $html
     * @param bool $useRegex
     * @return string
     */
    function add_image_caption(string $html, bool $useRegex = false): string
    {
        return $useRegex 
            ? \App\Helpers\ImageCaptionHelper::addFigcaptionRegex($html)
            : \App\Helpers\ImageCaptionHelper::addFigcaption($html);
    }
}