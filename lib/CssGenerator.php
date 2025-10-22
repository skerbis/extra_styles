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
        $borderColor = $style['border_color'];
        $borderWidth = $style['border_width'] ?? 1;
        $borderRadius = $style['border_radius'] ?? null;
        $isLight = (bool)$style['is_light'];
        
        $css[] = "/* {$style['name']} ({$type}) */";
        
        switch ($type) {
            case 'card':
                $css[] = ".uk-card-{$slug} {";
                $css[] = "    background-color: {$color};";
                if ($textColor) {
                    $css[] = "    color: {$textColor};";
                }
                if ($borderColor) {
                    $css[] = "    border: {$borderWidth}px solid {$borderColor};";
                }
                if ($borderRadius) {
                    $css[] = "    border-radius: {$borderRadius};";
                }
                if ($isLight) {
                    $css[] = "    color: #fff;";
                }
                $css[] = "}";
                
                // Hover-Effekt für verlinkte Cards
                $css[] = "a .uk-card-{$slug}:hover {";
                $css[] = "    box-shadow: 0 14px 25px rgba(0,0,0,0.16);";
                $css[] = "}";
                break;
                
            case 'section':
                $css[] = ".uk-section-{$slug} {";
                $css[] = "    background-color: {$color};";
                if ($textColor) {
                    $css[] = "    color: {$textColor};";
                }
                if ($borderRadius) {
                    $css[] = "    border-radius: {$borderRadius};";
                }
                if ($isLight) {
                    $css[] = "    color: #fff;";
                }
                $css[] = "}";
                break;
                
            case 'background':
                $css[] = ".uk-background-{$slug} {";
                $css[] = "    background-color: {$color};";
                if ($textColor) {
                    $css[] = "    color: {$textColor};";
                }
                if ($borderRadius) {
                    $css[] = "    border-radius: {$borderRadius};";
                }
                if ($isLight) {
                    $css[] = "    color: #fff;";
                }
                $css[] = "}";
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
