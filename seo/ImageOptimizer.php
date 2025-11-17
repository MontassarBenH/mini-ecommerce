<?php
// seo/ImageOptimizer.php - Image optimization utilities

class ImageOptimizer {
    
    /**
     * Generate responsive image srcset
     */
    public static function getResponsiveImageSrcset($imageUrl, $sizes = [400, 800, 1200]) {
        $srcset = [];
        
        foreach ($sizes as $size) {
            // For Unsplash images, we can add width parameter
            if (strpos($imageUrl, 'unsplash.com') !== false) {
                $url = $imageUrl . '&w=' . $size . '&q=80';
                $srcset[] = $url . ' ' . $size . 'w';
            }
        }
        
        return implode(', ', $srcset);
    }
    
    /**
     * Generate responsive image sizes attribute
     */
    public static function getResponsiveImageSizes() {
        return '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw';
    }
    
    /**
     * Get optimized image URL
     */
    public static function getOptimizedImageUrl($imageUrl, $width = 800, $quality = 80) {
        // For Unsplash images
        if (strpos($imageUrl, 'unsplash.com') !== false) {
            return $imageUrl . '&w=' . $width . '&q=' . $quality . '&fm=webp';
        }
        
        return $imageUrl;
    }
    
    /**
     * Generate WebP image tag with fallback
     */
    public static function getWebPImageTag($imageUrl, $alt, $class = '', $width = 800) {
        $webpUrl = self::getOptimizedImageUrl($imageUrl, $width);
        $fallbackUrl = self::getOptimizedImageUrl($imageUrl, $width);
        
        return sprintf(
            '<picture>
                <source srcset="%s" type="image/webp">
                <img src="%s" alt="%s" class="%s" loading="lazy" decoding="async">
            </picture>',
            htmlspecialchars($webpUrl),
            htmlspecialchars($fallbackUrl),
            htmlspecialchars($alt),
            htmlspecialchars($class)
        );
    }
    
    /**
     * Add lazy loading attributes
     */
    public static function getLazyLoadAttributes() {
        return 'loading="lazy" decoding="async"';
    }
}
?>