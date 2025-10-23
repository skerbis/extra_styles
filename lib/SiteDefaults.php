<?php
/**
 * Extra Styles AddOn
 * Site Defaults API Class
 */

namespace ExtraStyles;

use rex_addon;

class SiteDefaults
{
    /**
     * Gibt das Info-Button-Menü als HTML aus
     * 
     * Verwendung im Template:
     * <?= ExtraStyles\SiteDefaults::getInfoButtonMenu() ?>
     * 
     * @return string HTML des Info-Button-Menüs
     */
    public static function getInfoButtonMenu(): string
    {
        $addon = rex_addon::get('extra_styles');
        
        $icon = $addon->getConfig('info_button_icon', 'info');
        $ratio = $addon->getConfig('info_button_ratio', '1.5');
        $hiddenText = $addon->getConfig('info_button_hidden_text', 'Mehr Informationen');
        $title = $addon->getConfig('info_button_title', 'Mehr Informationen');
        $menuItems = $addon->getConfig('info_menu_items', []);
        
        // Ratio mit Punkt statt Komma sicherstellen
        $ratio = str_replace(',', '.', $ratio);
        
        $html = '<button class="uk-light" type="button" uk-icon="icon: ' . htmlspecialchars($icon) . '; ratio: ' . htmlspecialchars($ratio) . '">';
        $html .= '<span class="uk-hidden">' . htmlspecialchars($hiddenText) . '</span>';
        $html .= '</button>';
        $html .= '<div uk-drop="mode: click">';
        $html .= '<div class="uk-card uk-light uk-card-body uk-card-primary">';
        
        if ($title) {
            $html .= '<strong>' . htmlspecialchars($title) . '</strong>';
            if (!empty($menuItems)) {
                $html .= '<hr>';
            }
        }
        
        foreach ($menuItems as $item) {
            if (!empty($item['url']) && !empty($item['label'])) {
                $html .= '<span uk-icon="icon: ' . htmlspecialchars($item['icon']) . '"></span> ';
                $html .= '<a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['label']) . '</a>';
                $html .= '<hr>';
            }
        }
        
        // Letztes <hr> entfernen wenn vorhanden
        $html = rtrim($html, '<hr>');
        
        $html .= '</div>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Gibt die Logo-Beschriftung aus
     * 
     * Verwendung im Template:
     * <?= ExtraStyles\SiteDefaults::getLogoText() ?>
     * 
     * @return string Logo-Text
     */
    public static function getLogoText(): string
    {
        $addon = rex_addon::get('extra_styles');
        return $addon->getConfig('logo_text', '');
    }
    
    /**
     * Gibt einen einzelnen Config-Wert zurück
     * 
     * @param string $key Config-Key
     * @param mixed $default Default-Wert
     * @return mixed
     */
    public static function getConfig(string $key, $default = null)
    {
        $addon = rex_addon::get('extra_styles');
        return $addon->getConfig($key, $default);
    }
}
