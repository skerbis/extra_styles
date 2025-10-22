<?php
/**
 * Extra Styles AddOn
 * CSS Generator Class
 */

namespace ExtraStyles;

use rex;
use rex_file;
use rex_path;
use rex_sql;
use rex_addon;

class CssGenerator
{
    /**
     * Generiert die custom.css Datei aus der Datenbank
     * 
     * @return bool
     */
    public static function generate(): bool
    {
        $addon = rex_addon::get('extra_styles');
        $cssPath = rex_path::assets('addons/extra_styles/custom.css');
        
        try {
            $sql = rex_sql::factory();
            $sql->setQuery('SELECT * FROM ' . rex::getTable('extra_styles') . ' WHERE status = 1 ORDER BY priority ASC, name ASC');
            
            $css = [];
            $css[] = "/* Extra Styles - Custom CSS */";
            $css[] = "/* Automatisch generiert am " . date('d.m.Y H:i:s') . " */";
            $css[] = "";
            
            for ($i = 0; $i < $sql->getRows(); $i++) {
                $style = [
                    'slug' => $sql->getValue('slug'),
                    'type' => $sql->getValue('type'),
                    'color' => $sql->getValue('color'),
                    'text_color' => $sql->getValue('text_color'),
                    'link_color' => $sql->getValue('link_color'),
                    'border_color' => $sql->getValue('border_color'),
                    'border_width' => $sql->getValue('border_width'),
                    'border_radius' => $sql->getValue('border_radius'),
                    'is_light' => $sql->getValue('is_light'),
                    'name' => $sql->getValue('name'),
                ];
                $css[] = self::generateStyleCss($style);
                $sql->next();
            }
            
            $cssContent = implode("\n", $css);
            rex_file::put($cssPath, $cssContent);
            
            $addon->setConfig('css_generated', true);
            return true;
            
        } catch (\Exception $e) {
            \rex_logger::logException($e);
            return false;
        }
    }
    
    /**
     * Generiert CSS für einen einzelnen Stil
     * 
     * @param array $style
     * @return string
     */
    private static function generateStyleCss(array $style): string
    {
        $css = [];
        $slug = $style['slug'];
        $type = $style['type'];
        $color = $style['color'];
        $textColor = $style['text_color'];
        $linkColor = $style['link_color'];
        $borderColor = $style['border_color'];
        $borderWidth = $style['border_width'] ?? 1;
        $borderRadius = $style['border_radius'] ?? null;
        $isLight = (bool)$style['is_light'];
        
        $css[] = "/* {$style['name']} ({$type}) */";
        
        switch ($type) {
            case 'card':
                $css[] = ".uk-card-{$slug} {";
                $css[] = "    background-color: {$color} !important;";
                // Textfarbe: text_color hat Vorrang vor is_light
                if ($textColor) {
                    $css[] = "    color: {$textColor} !important;";
                } elseif ($isLight) {
                    $css[] = "    color: #fff !important;";
                }
                if ($borderColor) {
                    $css[] = "    border: {$borderWidth}px solid {$borderColor} !important;";
                }
                if ($borderRadius) {
                    $css[] = "    border-radius: {$borderRadius} !important;";
                }
                $css[] = "}";
                
                // Überschriften erben die Textfarbe
                if ($textColor || $isLight) {
                    $inheritColor = $textColor ?: '#fff';
                    $css[] = ".uk-card-{$slug} h1, .uk-card-{$slug} h2, .uk-card-{$slug} h3, .uk-card-{$slug} h4, .uk-card-{$slug} h5, .uk-card-{$slug} h6,";
                    $css[] = ".uk-card-{$slug} .uk-h1, .uk-card-{$slug} .uk-h2, .uk-card-{$slug} .uk-h3, .uk-card-{$slug} .uk-h4, .uk-card-{$slug} .uk-h5, .uk-card-{$slug} .uk-h6,";
                    $css[] = ".uk-card-{$slug} .uk-heading-small, .uk-card-{$slug} .uk-heading-medium, .uk-card-{$slug} .uk-heading-large, .uk-card-{$slug} .uk-heading-xlarge, .uk-card-{$slug} .uk-heading-2xlarge {";
                    $css[] = "    color: {$inheritColor} !important;";
                    $css[] = "}";
                }
                
                // Links: nur wenn link_color explizit gesetzt ist
                if ($linkColor) {
                    $css[] = ".uk-card-{$slug} a:not(.uk-button):not(.uk-badge) {";
                    $css[] = "    color: {$linkColor} !important;";
                    $css[] = "    text-decoration: underline;";
                    $css[] = "}";
                    $css[] = ".uk-card-{$slug} a:not(.uk-button):not(.uk-badge):hover {";
                    $css[] = "    opacity: 0.8;";
                    $css[] = "}";
                }
                
                // Hover-Effekt für verlinkte Cards
                $css[] = "a .uk-card-{$slug}:hover {";
                $css[] = "    box-shadow: 0 14px 25px rgba(0,0,0,0.16);";
                $css[] = "}";
                break;
                
            case 'section':
                $css[] = ".uk-section-{$slug} {";
                $css[] = "    background-color: {$color} !important;";
                // Textfarbe: text_color hat Vorrang vor is_light
                if ($textColor) {
                    $css[] = "    color: {$textColor} !important;";
                } elseif ($isLight) {
                    $css[] = "    color: #fff !important;";
                }
                if ($borderRadius) {
                    $css[] = "    border-radius: {$borderRadius} !important;";
                }
                $css[] = "}";
                
                // Überschriften erben die Textfarbe
                if ($textColor || $isLight) {
                    $inheritColor = $textColor ?: '#fff';
                    $css[] = ".uk-section-{$slug} h1, .uk-section-{$slug} h2, .uk-section-{$slug} h3, .uk-section-{$slug} h4, .uk-section-{$slug} h5, .uk-section-{$slug} h6,";
                    $css[] = ".uk-section-{$slug} .uk-h1, .uk-section-{$slug} .uk-h2, .uk-section-{$slug} .uk-h3, .uk-section-{$slug} .uk-h4, .uk-section-{$slug} .uk-h5, .uk-section-{$slug} .uk-h6,";
                    $css[] = ".uk-section-{$slug} .uk-heading-small, .uk-section-{$slug} .uk-heading-medium, .uk-section-{$slug} .uk-heading-large, .uk-section-{$slug} .uk-heading-xlarge, .uk-section-{$slug} .uk-heading-2xlarge {";
                    $css[] = "    color: {$inheritColor} !important;";
                    $css[] = "}";
                }
                
                // Links: nur wenn link_color explizit gesetzt ist
                if ($linkColor) {
                    $css[] = ".uk-section-{$slug} a:not(.uk-button):not(.uk-badge) {";
                    $css[] = "    color: {$linkColor} !important;";
                    $css[] = "    text-decoration: underline;";
                    $css[] = "}";
                    $css[] = ".uk-section-{$slug} a:not(.uk-button):not(.uk-badge):hover {";
                    $css[] = "    opacity: 0.8;";
                    $css[] = "}";
                }
                break;
                
            case 'background':
                $css[] = ".uk-background-{$slug} {";
                $css[] = "    background-color: {$color} !important;";
                // Textfarbe: text_color hat Vorrang vor is_light
                if ($textColor) {
                    $css[] = "    color: {$textColor} !important;";
                } elseif ($isLight) {
                    $css[] = "    color: #fff !important;";
                }
                if ($borderRadius) {
                    $css[] = "    border-radius: {$borderRadius} !important;";
                }
                $css[] = "}";
                
                // Überschriften erben die Textfarbe
                if ($textColor || $isLight) {
                    $inheritColor = $textColor ?: '#fff';
                    $css[] = ".uk-background-{$slug} h1, .uk-background-{$slug} h2, .uk-background-{$slug} h3, .uk-background-{$slug} h4, .uk-background-{$slug} h5, .uk-background-{$slug} h6,";
                    $css[] = ".uk-background-{$slug} .uk-h1, .uk-background-{$slug} .uk-h2, .uk-background-{$slug} .uk-h3, .uk-background-{$slug} .uk-h4, .uk-background-{$slug} .uk-h5, .uk-background-{$slug} .uk-h6,";
                    $css[] = ".uk-background-{$slug} .uk-heading-small, .uk-background-{$slug} .uk-heading-medium, .uk-background-{$slug} .uk-heading-large, .uk-background-{$slug} .uk-heading-xlarge, .uk-background-{$slug} .uk-heading-2xlarge {";
                    $css[] = "    color: {$inheritColor} !important;";
                    $css[] = "}";
                }
                
                // Links: nur wenn link_color explizit gesetzt ist
                if ($linkColor) {
                    $css[] = ".uk-background-{$slug} a:not(.uk-button):not(.uk-badge) {";
                    $css[] = "    color: {$linkColor} !important;";
                    $css[] = "    text-decoration: underline;";
                    $css[] = "}";
                    $css[] = ".uk-background-{$slug} a:not(.uk-button):not(.uk-badge):hover {";
                    $css[] = "    opacity: 0.8;";
                    $css[] = "}";
                }
                break;
                
            case 'border':
                $css[] = ".uk-border-{$slug} {";
                if ($borderColor) {
                    $css[] = "    border: {$borderWidth}px solid {$borderColor};";
                } else {
                    $css[] = "    border: {$borderWidth}px solid {$color};";
                }
                if ($borderRadius) {
                    $css[] = "    border-radius: {$borderRadius};";
                }
                $css[] = "}";
                break;
        }
        
        $css[] = "";
        return implode("\n", $css);
    }
    
    /**
     * Gibt den Pfad zur custom.css zurück
     * 
     * @return string
     */
    public static function getCssPath(): string
    {
        return rex_path::assets('addons/extra_styles/custom.css');
    }
    
    /**
     * Gibt die URL zur custom.css zurück
     * 
     * @return string
     */
    public static function getCssUrl(): string
    {
        return rex_addon::get('extra_styles')->getAssetsUrl('custom.css');
    }
}
