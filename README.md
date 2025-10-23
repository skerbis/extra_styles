# Extra Styles AddOn für REDAXO

Ein benutzerfreundliches AddOn für REDAXO 5.x, mit dem Redakteure und Admins zusätzliche Farben und Stile für UIKit3-Module erstellen können.

## Features

✨ **Einfache Bedienung**: Intuitive Oberfläche mit Live-Preview  
🎨 **Moderner Color Picker**: Farben visuell auswählen  
🔄 **Dynamische CSS-Generierung**: Automatische Erstellung der custom.css  
📦 **UIKit3 kompatibel**: Erweitert die Standard-UIKit-Styles  
🛡️ **Fallback-sicher**: Bei Löschung wird auf "default" zurückgefallen  
🎯 **Typisiert**: Card, Section, Background, Border Styles  

## Installation

1. AddOn über den Installer oder manuell installieren
2. AddOn aktivieren
3. Unter "AddOns" → "Extra Styles" eigene Styles anlegen

## Verwendung

### Backend: Styles verwalten

1. Navigiere zu **AddOns** → **Extra Styles**
2. Klicke auf **"Stil hinzufügen"**
3. Fülle das Formular aus:
   - **Name**: Sprechender Name (z.B. "Dunkelblau", "Akzentfarbe")
   - **Slug**: Wird automatisch generiert (z.B. "dunkelblau")
   - **Typ**: Card, Section, Background oder Border
   - **Hintergrundfarbe**: Hauptfarbe (Hex-Code)
   - **Transparenz (Alpha)**: 0.00 (transparent) bis 1.00 (deckend)
   - **Backdrop Blur**: Verwischt den Hintergrund (Glasmorphismus-Effekt, 0-100px)
   - **Textfarbe**: Optional, für bessere Lesbarkeit
   - **Linkfarbe**: Optional, für visuelle Unterscheidung von Links
   - **Rahmenfarbe**: Optional, für Cards mit Rahmen
   - **Rahmenstärke**: In Pixel (Standard: 1)
   - **Border Radius**: Abgerundete Ecken (z.B. "8px")
   - **Helle Schrift**: Aktivieren für weiße Schrift
   - **Reihenfolge**: Sortierung in der Auswahlliste
   - **Status**: Aktiv/Inaktiv

4. Die **Live-Preview** zeigt sofort, wie der Stil aussieht
5. **Speichern** – die custom.css wird automatisch generiert

### Frontend: Styles in Modulen verwenden

#### Methode 1: Mit der API-Klasse (empfohlen)

```php
<?php
use ExtraStyles\ExtraStyles;
use FriendsOfRedaxo\MForm;

$MForm = MForm::factory()
    ->addSelectField("1.0.ukColor")
    ->setLabel('Farbe:')
    ->setAttribute('class', 'selectpicker')
    ->setOptions(ExtraStyles::getSelectOptions('card'));

echo $MForm->show();
```

#### Methode 2: Manuelle Integration

```php
<?php
use ExtraStyles\ExtraStyles;

// Alle Card-Styles abrufen
$cardStyles = ExtraStyles::getAll('card');

// In Select-Optionen umwandeln
$options = ['default' => 'Standard'];
foreach ($cardStyles as $style) {
    $options[$style['slug']] = $style['name'];
}

// In MForm verwenden
$MForm = MForm::factory()
    ->addSelectField("1.0.cardStyle")
    ->setLabel('Card-Stil:')
    ->setOptions($options);
```

### Beispiel: Oejv Cards Modul erweitern

**Vorher (input.php):**
```php
->addSelectField("$id.0.ukColor")
->setLabel('Farbe:')
->setOptions(array(
    'default' => 'Standard',
    'primary' => 'Hauptfarbe',
    'secondary' => 'Sekundär',
    'muted' => 'Muted',
))
```

**Nachher (input.php):**
```php
->addSelectField("$id.0.ukColor")
->setLabel('Farbe:')
->setOptions(ExtraStyles::getSelectOptions('card'))
```

Das war's! Die Custom Styles werden automatisch hinzugefügt.

### Output im Template

Die generierten CSS-Klassen folgen dem UIKit3-Standard:

- **Cards**: `.uk-card-{slug}`
- **Sections**: `.uk-section-{slug}`
- **Backgrounds**: `.uk-background-{slug}`
- **Borders**: `.uk-border-{slug}`

Beispiel:
```html
<!-- Style "dunkelblau" vom Typ "card" -->
<div class="uk-card uk-card-dunkelblau">
    <div class="uk-card-body">
        Inhalt
    </div>
</div>
```

### CSS-Datei im Template einbinden

Das AddOn bindet die CSS-Datei automatisch ein. Sie können aber auch manuell mit Cachebuster einbinden:

```php
<!-- Kompletter Link-Tag -->
<?= ExtraStyles\ExtraStyles::getCssTag() ?>

<!-- Oder nur die URL mit Cachebuster -->
<link rel="stylesheet" href="<?= ExtraStyles\ExtraStyles::getCssUrl() ?>">
```

Der Cachebuster basiert auf dem Änderungszeitpunkt der CSS-Datei und stellt sicher, dass Browser immer die aktuellste Version laden.

Beispiel-Output:
```html
<link rel="stylesheet" href="/assets/addons/extra_styles/custom.css?v=1729717845">
```

## Einstellungen

### Style-Typen aktivieren/deaktivieren

Unter **Einstellungen** können Sie festlegen, welche Style-Typen in der Verwaltung verfügbar sein sollen:

- ☑️ **Cards aktivieren**: uk-card-* Klassen für Kacheln und Content-Boxen
- ☑️ **Sections aktivieren**: uk-section-* Klassen für große Abschnitte mit Padding
- ☑️ **Backgrounds aktivieren**: uk-background-* Klassen für Hintergründe ohne Padding
- ☑️ **Borders aktivieren**: uk-border-* Klassen nur für Rahmen

### Individuelle CSS-Stile

Zusätzlich zu den generierten Styles können Sie **individuelle CSS-Regeln** eingeben:

1. Navigiere zu **Einstellungen**
2. Scrolle zu **Individuelle CSS-Stile**
3. Gib deine CSS-Regeln ein (mit CodeMirror-Editor)
4. Speichern

Die individuellen CSS-Regeln werden **am Anfang** der generierten CSS-Datei eingefügt und stehen im gesamten Frontend zur Verfügung.

**Beispiel:**
```css
.aspect-ratio-16-9 {
    display: flow-root;
    position: relative;
}

.aspect-ratio-16-9::before {
    content: '';
    float: left;
    padding-bottom: 56.25%;
}

@media screen and (max-width: 1499px) {
    img.logo { 
        height: 100px; 
        position: absolute; 
        left: 4%; 
        top: 0px; 
        z-index: 20;
    }   
}
```

## API-Referenz

### ExtraStyles Klasse

```php
use ExtraStyles\ExtraStyles;

// Alle Styles abrufen
$allStyles = ExtraStyles::getAll();

// Nur Card-Styles
$cardStyles = ExtraStyles::getAll('card');

// Select-Optionen für MForm (inkl. Standard-UIKit-Styles)
$options = ExtraStyles::getSelectOptions('card');

// Style nach ID
$style = ExtraStyles::getById(5);

// Style nach Slug
$style = ExtraStyles::getBySlug('dunkelblau');

// Style löschen
ExtraStyles::delete(5);

// CSS-URL mit Cachebuster
$cssUrl = ExtraStyles::getCssUrl();
// Ergebnis: /assets/addons/extra_styles/custom.css?v=1729717845

// Kompletter Link-Tag mit Cachebuster
$cssTag = ExtraStyles::getCssTag();
// Ergebnis: <link rel="stylesheet" href="/assets/addons/extra_styles/custom.css?v=1729717845">
```

### CssGenerator Klasse

```php
use ExtraStyles\CssGenerator;

// CSS manuell neu generieren
CssGenerator::generate();
```

## Import / Export

Mit der Import/Export-Funktion können Sie alle Stildefinitionen **und** die individuellen Admin-Styles als JSON-Datei sichern und zwischen Installationen austauschen.

### Styles exportieren

1. Navigiere zu **Import / Export**
2. Klicke auf **JSON herunterladen**
3. Eine JSON-Datei mit allen Styles und Custom CSS wird heruntergeladen

**Export enthält:**
- ✅ Alle Stildefinitionen (Card, Section, Background, Border)
- ✅ Individuelle Admin-Styles (Custom CSS)
- ✅ Alle Einstellungen (Farben, Textfarben, Linkfarben, etc.)

### Styles importieren

1. Navigiere zu **Import / Export**
2. Wähle eine JSON-Datei aus
3. Klicke auf **Importieren**

**Wichtig**: 
- Vor dem Import wird automatisch ein **Backup** erstellt
- Backups werden in `data/addons/extra_styles/backups/` gespeichert
- Backups enthalten ebenfalls die Custom CSS
- Existierende Styles (gleicher Slug) werden aktualisiert
- Neue Styles werden hinzugefügt
- Custom CSS wird überschrieben (wenn im Import vorhanden)

## Style-Typen

### Card (`uk-card-{slug}`)
Für UIKit3 Cards (Kacheln). Ersetzt/ergänzt:
- `uk-card-default`
- `uk-card-primary`
- `uk-card-secondary`
- `uk-card-muted`

### Section (`uk-section-{slug}`)
Für Sektionen/Abschnitte. Ersetzt/ergänzt:
- `uk-section-default`
- `uk-section-primary`
- `uk-section-secondary`
- `uk-section-muted`

### Background (`uk-background-{slug}`)
Für Hintergründe. Ersetzt/ergänzt:
- `uk-background-default`
- `uk-background-primary`
- `uk-background-secondary`
- `uk-background-muted`

### Border (`uk-border-{slug}`)
Nur für Rahmen, ohne Hintergrund.

## Tipps für Redakteure

1. **Sprechende Namen verwenden**: "Dunkelblau" statt "Farbe 1"
2. **Live-Preview nutzen**: Sehen Sie sofort, wie es aussieht
3. **Textfarbe beachten**: Für dunkle Hintergründe "Helle Schrift" aktivieren oder benutzerdefinierte Textfarbe setzen
4. **Linkfarbe optional**: Separate Linkfarbe für bessere Unterscheidung vom Text
5. **Barrierefreiheit**: Die Preview warnt bei unzureichendem Kontrast (WCAG 2.1)
6. **Rahmen optional**: Border-Farbe nur setzen, wenn gewünscht
7. **Border Radius**: Abgerundete Ecken in Pixeln (z.B. 8)
8. **Status**: Inaktive Styles bleiben erhalten, werden aber nicht angezeigt
9. **Individuelle Styles**: Für spezielle CSS-Regeln die Einstellungsseite nutzen

## Transparenz und Glasmorphismus

Das AddOn unterstützt moderne Design-Effekte:

### Alpha-Transparenz
- Setzen Sie die **Transparenz** zwischen 0.00 (vollständig transparent) und 1.00 (vollständig deckend)
- Perfekt für Overlays, semi-transparente Cards und moderne UI-Designs
- Die Farbe wird automatisch als RGBA ausgegeben

### Backdrop Blur (Glasmorphismus)
- Aktivieren Sie den **Backdrop Blur** für einen Verwischungseffekt des Hintergrunds
- Empfohlene Werte: 5-20px für subtile Effekte, 20-50px für starke Verwischung
- Funktioniert am besten mit semi-transparenten Hintergründen (Alpha < 1.0)
- Erstellt den beliebten "Frosted Glass" Effekt

**Beispiel-Kombination:**
- Hintergrundfarbe: `#ffffff`
- Alpha: `0.3` (30% Deckkraft)
- Backdrop Blur: `10px`
- Ergebnis: Milchglasartiger Effekt mit durchscheinendem Hintergrund

## Barrierefreiheit (Accessibility)

Das AddOn unterstützt Sie bei der Erstellung barrierefreier Farbkombinationen:

- **Automatische Kontrastprüfung**: Die Live-Preview zeigt Warnungen bei unzureichendem Kontrast
- **WCAG 2.1 konform**: Mindestens 4.5:1 für normalen Text, 3:1 für großen Text
- **Separate Linkfarbe**: Links können sich deutlich vom Fließtext abheben
- **Visuelle Hinweise**: Grüne ✓ bei ausreichendem Kontrast, gelbe ⚠ bei Problemen

**Wichtig bei Transparenz:** Bei sehr transparenten Hintergründen kann der Kontrast je nach darunter liegendem Inhalt variieren. Testen Sie diese Kombinationen immer visuell!

## Technische Details

### Datenbank

Tabelle: `rex_extra_styles`

Felder:
- `id`: Primärschlüssel
- `name`: Anzeigename
- `slug`: CSS-Klassen-Name (eindeutig)
- `type`: card, section, background, border
- `color`: Hintergrundfarbe (Hex)
- `color_alpha`: Alpha-Transparenz (Decimal 0.00-1.00, Standard: 1.00)
- `backdrop_blur`: Backdrop Blur in Pixel (Integer 0-100, Standard: 0)
- `text_color`: Textfarbe (Hex, optional)
- `link_color`: Linkfarbe (Hex, optional)
- `border_color`: Rahmenfarbe (Hex, optional)
- `border_width`: Rahmenstärke (Integer, Standard: 1)
- `border_radius`: Border Radius (String, z.B. "8px")
- `is_light`: Helle Schrift (Boolean)
- `priority`: Sortierung (Integer)
- `status`: Aktiv/Inaktiv (Boolean)
- `createdate`, `updatedate`, `createuser`, `updateuser`

### CSS-Generierung

Die `custom.css` wird automatisch generiert:
- Bei Speichern/Löschen eines Styles
- Bei Änderung der individuellen CSS-Stile
- Bei AddOn-Update
- Manuell über "CSS regenerieren"

**Struktur der generierten CSS-Datei:**
1. Header mit Generierungszeitpunkt
2. **Individuelle CSS-Stile** (falls vorhanden)
3. Generierte Styles aus der Datenbank

**CSS-Spezifität:**
- Alle Farbdeklarationen verwenden `!important` um UIKit's `.uk-light` zu überschreiben
- Überschriften (h1-h6) erben die Textfarbe
- Links erhalten separate Farbe nur wenn `link_color` gesetzt ist

Speicherort: `assets/addons/extra_styles/custom.css`

Die Datei wird im Frontend und Backend automatisch eingebunden.

### Dependencies

- **REDAXO**: >= 5.13
- **PHP**: >= 8.0
- **UIKit3**: Im REDAXO integriert
- **Pickr**: Color Picker (lokal integriert, 23KB)
- **CodeMirror**: Für CSS-Editor (REDAXO-integriert)

## Entwickler-Hinweise

### Extension Points

Das AddOn triggert folgende Extension Points:

```php
// Nach dem Speichern
rex_extension::register('REX_FORM_SAVED', function($ep) {
    // CSS wird automatisch regeneriert
});

// Nach dem Löschen
rex_extension::register('REX_FORM_DELETED', function($ep) {
    // CSS wird automatisch regeneriert
});
```

### Eigene Styles programmatisch anlegen

```php
$sql = rex_sql::factory();
$sql->setTable(rex::getTable('extra_styles'));
$sql->setValue('name', 'Mein Style');
$sql->setValue('slug', 'mein-style');
$sql->setValue('type', 'card');
$sql->setValue('color', '#ff0000');
$sql->setValue('status', 1);
$sql->setValue('createdate', date('Y-m-d H:i:s'));
$sql->setValue('updatedate', date('Y-m-d H:i:s'));
$sql->setValue('createuser', rex::getUser()->getValue('login'));
$sql->setValue('updateuser', rex::getUser()->getValue('login'));
$sql->insert();

// CSS regenerieren
ExtraStyles\CssGenerator::generate();
```

## Site Defaults

Das AddOn bietet eine **Site Defaults** Seite für seitenweite Einstellungen:

### Info-Button-Menü

Erstellen Sie ein konfigurierbares Info-Button-Menü mit UIKit-Drop-Down:

**Backend-Konfiguration:**
1. Navigiere zu **Extra Styles** → **Site Defaults**
2. Konfiguriere Button-Icon, Größe und versteckten Text
3. Füge Menüpunkte hinzu (Icon, URL, Bezeichnung)

**Template-Verwendung:**
```php
<?= ExtraStyles\SiteDefaults::getInfoButtonMenu() ?>
```

**Ausgabe:**
```html
<button class="uk-light" type="button" uk-icon="icon: info; ratio: 1.5">
    <span class="uk-hidden">Mehr Informationen</span>
</button>
<div uk-drop="mode: click">
    <div class="uk-card uk-light uk-card-body uk-card-primary">
        <strong>Mehr Informationen</strong>
        <hr>
        <span uk-icon="icon: instagram"></span> <a href="...">Instagram</a>
        <hr>
        <!-- weitere Menüpunkte -->
    </div>
</div>
```

### Logo-Beschriftung

**Template-Verwendung:**
```php
<?= ExtraStyles\SiteDefaults::getLogoText() ?>
```

### Berechtigungen

Die Site Defaults Seite kann für Redakteure freigegeben werden:
- **Berechtigung**: `extra_styles[site_defaults]`
- Unter **Benutzer** → **Rollen** → Rechte vergeben

## Troubleshooting

### CSS wird nicht geladen
1. Prüfen Sie, ob die `custom.css` existiert: `assets/addons/extra_styles/custom.css`
2. Klicken Sie auf "CSS regenerieren"
3. Prüfen Sie die Dateiberechtigungen

### Styles werden nicht angezeigt
1. Prüfen Sie den **Status**: Muss "Aktiv" sein
2. Leeren Sie den Browser-Cache
3. Prüfen Sie, ob die CSS-Klasse korrekt im HTML ist

### Color Picker funktioniert nicht
1. Prüfen Sie die Browser-Konsole auf JavaScript-Fehler
2. Stellen Sie sicher, dass Pickr geladen wird (CDN)
3. Deaktivieren Sie Browser-Extensions

## Support

Bei Fragen oder Problemen:
- GitHub Issues
- REDAXO Slack: #addons
- Forum: https://www.redaxo.org/forum/

## Lizenz

MIT License

## Credits

- **UIKit3**: https://getuikit.com/
- **Pickr**: https://github.com/Simonwep/pickr
- **REDAXO**: https://www.redaxo.org/

---

**Version**: 1.0.0  
**Autor**: REDAXO AddOn  
**Erfordert**: REDAXO >= 5.13, PHP >= 8.0
