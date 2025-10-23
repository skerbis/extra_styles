/**
 * Extra Styles - Backend JavaScript
 * Verwendet Pickr (moderner Color Picker)
 */

jQuery(function($) {
    
    console.log('Extra Styles Backend JS loaded');
    
    // Kontrast-Berechnung (WCAG 2.1)
    function getContrastRatio(color1, color2) {
        const getLuminance = (hexColor) => {
            // Hex zu RGB
            const hex = hexColor.replace('#', '');
            const r = parseInt(hex.substr(0, 2), 16) / 255;
            const g = parseInt(hex.substr(2, 2), 16) / 255;
            const b = parseInt(hex.substr(4, 2), 16) / 255;
            
            // Relative Luminanz berechnen
            const rsRGB = r <= 0.03928 ? r / 12.92 : Math.pow((r + 0.055) / 1.055, 2.4);
            const gsRGB = g <= 0.03928 ? g / 12.92 : Math.pow((g + 0.055) / 1.055, 2.4);
            const bsRGB = b <= 0.03928 ? b / 12.92 : Math.pow((b + 0.055) / 1.055, 2.4);
            
            return 0.2126 * rsRGB + 0.7152 * gsRGB + 0.0722 * bsRGB;
        };
        
        const lum1 = getLuminance(color1);
        const lum2 = getLuminance(color2);
        
        const lighter = Math.max(lum1, lum2);
        const darker = Math.min(lum1, lum2);
        
        return (lighter + 0.05) / (darker + 0.05);
    }
    
    // Preview Update Funktion
    function updatePreview() {
        const previewBox = $('#preview-box');
        if (!previewBox.length) {
            console.log('Preview box nicht gefunden');
            return;
        }
        
        console.log('Updating preview...');
        
        // Inputs finden - mit verschiedenen Selektoren
        const colorInput = $('input[id*="-color"]:not([id*="text"]):not([id*="link"]):not([id*="border"])').first();
        const textColorInput = $('input[id*="text-color"]').first();
        const linkColorInput = $('input[id*="link-color"]').first();
        const borderColorInput = $('input[id*="border-color"]').first();
        const borderWidthInput = $('input[id*="border-width"]').first();
        const borderRadiusInput = $('input[id*="border-radius"]').first();
        const isLightSelect = $('select[id*="is-light"]').first();
        
        console.log('Color input value:', colorInput.val());
        console.log('Is light select value:', isLightSelect.val());
        
        // Styles anwenden
        let styles = {
            padding: '30px',
            minHeight: '150px',
            transition: 'all 0.3s ease'
        };
        
        // Backdrop Blur Input
        const backdropBlurInput = $('input[id*="backdrop-blur"]').first();
        const backdropBlur = parseInt(backdropBlurInput.val()) || 0;
        
        // Hintergrundfarbe (HEX oder RGBA direkt aus color-Feld)
        const bgColor = colorInput.val() || '#f5f5f5';
        styles.backgroundColor = bgColor;
        
        // Backdrop Filter
        if (backdropBlur > 0) {
            styles.backdropFilter = `blur(${backdropBlur}px)`;
            styles.webkitBackdropFilter = `blur(${backdropBlur}px)`;
        }
        
        // Textfarbe bestimmen
        let textColor = '#333';
        if (textColorInput.val()) {
            textColor = textColorInput.val();
        } else if (isLightSelect.val() == '1') {
            textColor = '#fff';
        }
        
        styles.color = textColor;
        
        // Rahmen
        if (borderColorInput.val() && borderWidthInput.val() > 0) {
            styles.border = borderWidthInput.val() + 'px solid ' + borderColorInput.val();
        }
        
        // Border Radius
        if (borderRadiusInput.val()) {
            styles.borderRadius = borderRadiusInput.val();
        } else {
            styles.borderRadius = '0'; // Standard
        }
        
        previewBox.css(styles);
        
        // Textfarbe auf ALLE Kindelemente anwenden (wichtig für h3, p, small etc.)
        previewBox.find('*').not('a').css('color', textColor);
        
        // Linkfarbe separat anwenden
        let linkColor = textColor; // Standard: gleich wie Text
        if (linkColorInput.val()) {
            linkColor = linkColorInput.val();
        }
        previewBox.find('a').css({
            'color': linkColor,
            'text-decoration': 'underline'
        });
        
        // Kontrast-Prüfung (WCAG AA)
        let warnings = [];
        
        // Text-Kontrast prüfen (WCAG AA: 4.5:1 für normalen Text)
        const textContrast = getContrastRatio(bgColor, textColor);
        if (textContrast < 4.5) {
            warnings.push('⚠️ <strong>Text-Kontrast zu gering</strong>: ' + textContrast.toFixed(2) + ':1 (mindestens 4.5:1 für WCAG AA)');
        }
        
        // Link-Kontrast prüfen
        const linkContrast = getContrastRatio(bgColor, linkColor);
        if (linkContrast < 4.5) {
            warnings.push('⚠️ <strong>Link-Kontrast zu gering</strong>: ' + linkContrast.toFixed(2) + ':1 (mindestens 4.5:1 für WCAG AA)');
        }
        
        // Link vs. Text Unterschied prüfen
        if (linkColorInput.val() && linkColor !== textColor) {
            const linkTextContrast = getContrastRatio(linkColor, textColor);
            if (linkTextContrast < 3) {
                warnings.push('💡 <strong>Link-Text-Unterschied gering</strong>: ' + linkTextContrast.toFixed(2) + ':1 (mindestens 3:1 empfohlen)');
            }
        }
        
        // Warnung anzeigen/verstecken
        let warningContainer = $('#a11y-warning');
        if (!warningContainer.length) {
            warningContainer = $('<div id="a11y-warning" style="margin-top: 15px; padding: 12px; border-radius: 4px; font-size: 13px; line-height: 1.6;"></div>');
            $('#extra-styles-preview-wrapper').append(warningContainer);
        }
        
        if (warnings.length > 0) {
            warningContainer.html(warnings.join('<br>')).css({
                background: '#fff3cd',
                border: '1px solid #ffc107',
                color: '#856404'
            }).show();
        } else {
            warningContainer.html('✅ <strong>Barrierefreiheit</strong>: Alle Kontraste erfüllen WCAG AA').css({
                background: '#d4edda',
                border: '1px solid #28a745',
                color: '#155724'
            }).show();
        }
        
        console.log('Preview updated with styles:', styles, 'Text color:', textColor, 'Link color:', linkColor);
        console.log('Contrast - Text:', textContrast.toFixed(2), 'Link:', linkContrast.toFixed(2));
    }
    
    // Globale Funktion für externe Aufrufe
    window.updateExtraStylesPreview = updatePreview;
    
    // Color Picker initialisieren
    const colorInputs = document.querySelectorAll('[data-colorpicker="true"]');
    const pickrInstances = [];
    
    colorInputs.forEach((input, index) => {
        // Container für Pickr erstellen
        const pickrContainer = document.createElement('div');
        pickrContainer.className = 'color-picker-wrapper';
        input.parentNode.insertBefore(pickrContainer, input.nextSibling);
        
        // Pickr initialisieren
        const pickr = Pickr.create({
            el: pickrContainer,
            theme: 'nano',
            default: input.value || '#ffffff',
            
            swatches: [
                '#003366', // DPSG Blau
                '#0066cc', // Hellblau
                '#009999', // Türkis  
                '#006633', // Dunkelgrün
                '#669900', // Hellgrün
                '#cc6600', // Orange
                '#cc0000', // Rot
                '#990066', // Lila
                '#666666', // Grau
                '#333333', // Dunkelgrau
                '#f8f8f8', // Hell
                '#ffffff', // Weiß
            ],
            
            components: {
                preview: true,
                opacity: true,
                hue: true,
                
                interaction: {
                    hex: true,
                    rgba: true,
                    hsla: false,
                    hsva: false,
                    cmyk: false,
                    input: true,
                    clear: true,
                    save: true
                }
            }
        });
        
        // Events
        pickr.on('save', (color, instance) => {
            if (color) {
                // Speichere als HEXA (mit Alpha) oder RGBA
                const rgba = color.toRGBA();
                if (rgba[3] < 1) {
                    // Mit Transparenz: RGBA speichern
                    input.value = `rgba(${Math.round(rgba[0])}, ${Math.round(rgba[1])}, ${Math.round(rgba[2])}, ${rgba[3].toFixed(2)})`;
                } else {
                    // Ohne Transparenz: HEX speichern
                    input.value = color.toHEXA().toString().substring(0, 7); // nur #RRGGBB ohne Alpha
                }
                console.log('Pickr saved:', input.value);
                updatePreview();
            }
            pickr.hide();
        });
        
        pickr.on('clear', (instance) => {
            input.value = '';
            console.log('Pickr cleared');
            updatePreview();
        });
        
        pickr.on('change', (color, source, instance) => {
            if (color) {
                // Bei Änderung auch sofort aktualisieren
                const rgba = color.toRGBA();
                if (rgba[3] < 1) {
                    input.value = `rgba(${Math.round(rgba[0])}, ${Math.round(rgba[1])}, ${Math.round(rgba[2])}, ${rgba[3].toFixed(2)})`;
                } else {
                    input.value = color.toHEXA().toString().substring(0, 7);
                }
                console.log('Pickr changed:', input.value);
                updatePreview();
            }
        });
        
        // Input-Änderungen an Pickr weitergeben
        input.addEventListener('input', function() {
            console.log('Input changed:', this.value);
            if (this.value) {
                try {
                    pickr.setColor(this.value);
                } catch(e) {
                    console.log('Invalid color:', this.value);
                }
            }
            updatePreview();
        });
        
        pickrInstances.push(pickr);
    });
    
    // Preview bei allen Input-Änderungen aktualisieren mit Event Delegation
    $(document).on('change input keyup', 'input[id*="color"], input[id*="backdrop"], input[id*="border"], input[id*="radius"], select[id*="is-light"]', function(e) {
        console.log('Input event detected:', e.type, this.id);
        updatePreview();
    });
    
    // Initiale Preview nach kurzer Verzögerung
    setTimeout(function() {
        console.log('Initial preview update');
        updatePreview();
    }, 800);
    
});  // Ende jQuery ready
