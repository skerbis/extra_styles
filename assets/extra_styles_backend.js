/**
 * Extra Styles - Backend JavaScript
 * Verwendet Pickr (moderner Color Picker)
 */

jQuery(function($) {
    
    console.log('Extra Styles Backend JS loaded');
    
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
        
        // Hintergrundfarbe
        if (colorInput.val()) {
            styles.backgroundColor = colorInput.val();
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
        
        console.log('Preview updated with styles:', styles, 'Text color:', textColor, 'Link color:', linkColor);
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
                opacity: false,
                hue: true,
                
                interaction: {
                    hex: true,
                    rgba: false,
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
                input.value = color.toHEXA().toString();
                console.log('Pickr saved:', color.toHEXA().toString());
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
                input.value = color.toHEXA().toString();
                console.log('Pickr changed:', color.toHEXA().toString());
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
    $(document).on('change input keyup', 'input[id*="color"], input[id*="border"], input[id*="radius"], select[id*="is-light"]', function(e) {
        console.log('Input event detected:', e.type, this.id);
        updatePreview();
    });
    
    // Initiale Preview nach kurzer Verzögerung
    setTimeout(function() {
        console.log('Initial preview update');
        updatePreview();
    }, 800);
    
});  // Ende jQuery ready
