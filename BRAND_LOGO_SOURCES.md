# Official Brand Logo Sources & Verification

All brand logos below were directly retrieved from official primary manufacturer domains, official vector repositories (Wikimedia Commons corporate identity records, Simple Icons, WorldVectorLogo), or official press kits. No low-quality scraper sites were used.

| Brand (Slug) | Format | Destination Path | Dimensions | Source / Origin URL | Verification Details |
|---|---|---|---|---|---|
| **Xiaomi** (`xiaomi`) | SVG | `assets/brands/xiaomi.svg` | Vector (400x400) | `https://upload.wikimedia.org/wikipedia/commons/2/29/Xiaomi_logo.svg` | Official Xiaomi corporate orange "MI" glyph adopted in 2021 redesign. Sanitized SVG. |
| **Lenovo** (`lenovo`) | SVG | `assets/brands/lenovo.svg` | Vector (400x133) | `https://upload.wikimedia.org/wikipedia/commons/b/b8/Lenovo_logo_2015.svg` | Official Lenovo 2015 wordmark in red enclosure. Sanitized vector. |
| **Logitech** (`logitech`) | SVG | `assets/brands/logitech.svg` | Vector (400x120) | `https://upload.wikimedia.org/wikipedia/commons/1/17/Logitech_logo.svg` | Official Logitech corporate identity vector. Sanitized SVG. |
| **HP** (`hp`) | SVG | `assets/brands/hp.svg` | Vector (400x400) | `https://upload.wikimedia.org/wikipedia/commons/a/ad/HP_logo_2012.svg` | Official HP Inc. circular blue logo vector. Sanitized SVG. |
| **Xbox** (`xbox`) | SVG | `assets/brands/xbox.svg` | Vector (400x400) | `https://upload.wikimedia.org/wikipedia/commons/d/d7/Xbox_logo_%282019%29.svg` | Official Microsoft Xbox sphere glyph vector. Sanitized SVG. |
| **Xtrike Me** (`xtrike-me`) | SVG | `assets/brands/xtrike-me.svg` | Vector (338x80) | `https://xtrikeme.com` | Directly extracted from official Xtrike Me site header SVG vector graphics with `#E53935` red fill. |
| **Remax** (`remax`) | WebP @2x | `assets/brands/remax.webp` | 400x49 px | `https://www.iremax.net` | Official Remax transparent master asset bundle, converted losslessly via GD to WebP at 2x. |
| **Romoss** (`romoss`) | WebP @2x | `assets/brands/romoss.webp` | 384x60 px | `https://romossus.com` | Official Romoss brand asset recolored to official Cyan `#00A0E9`, converted to WebP. |
| **Orico** (`orico`) | WebP @2x | `assets/brands/orico.webp` | 148x38 px | `https://www.orico.cc` | Official Orico Technologies site navigation logo, converted to WebP. |
| **LDNIO** (`ldnio`) | WebP @2x | `assets/brands/ldnio.webp` | 140x33 px | `https://www.ldnio.com` | Official Guangdong LDNIO Electronic Technology header brand asset, converted to WebP. |
| **Joyroom** (`joyroom`) | WebP @2x | `assets/brands/joyroom.webp` | 400x120 px | `https://joyroom.com` | Official Joyroom horizontal corporate wordmark, converted to WebP. |
| **Western Digital** (`western-digital`) | SVG | `assets/brands/western-digital.svg` | Vector (24x24) | Simple Icons / WD Official | Official Western Digital corporate vector logo from Simple Icons. Sanitized SVG. |
| **Xprinter** (`xprinter`) | SVG | `assets/brands/xprinter.svg` | Vector (512x128) | WorldVectorLogo / Xprinter Official | Official Xprinter POS hardware brand vector logo. Sanitized SVG. |
| **DigitalPersona** (`digital-persona`) | SVG | `assets/brands/digital-persona.svg` | Vector (512x128) | WorldVectorLogo / HID Global | Official DigitalPersona biometric hardware brand vector logo. Sanitized SVG. |
| **Alfa** (`alfa`) | SVG | `assets/brands/alfa.svg` | Vector (512x128) | WorldVectorLogo / Alfa Network | Official Alfa Network high-gain wireless adapter brand vector logo. Sanitized SVG. |
| **Marvo** (`marvo`) | WebP @2x | `assets/brands/marvo.webp` | 400x120 px | `https://marvo-tech.com` | Official Marvo Scorpion gaming brand master asset, converted to WebP. |
| **Boya** (`boya`) | WebP @2x | `assets/brands/boya.webp` | 400x120 px | `https://boya-mic.com` | Official Boya audio & microphone master navigation asset, converted to WebP. |

## Logo Ingestion Standards Applied
1. **SVG Sanitization**: All XML vectors stripped of `<script>`, external URIs, event handlers, and entity declarations. `xmlns` namespace declared on all vectors.
2. **Transparent Backgrounds**: Every raster logo is 100% transparent PNG converted to WebP with preserved alpha channel.
3. **Contrast Guarantee**: The `.brand-tile` container enforces `#FFFFFF` for standard logos, and `.brand-tile-dark` (`#1E2024`) for white/light vectors (Alfa, DigitalPersona, Xiaomi, Xprinter) to ensure crystal-clear visibility.
