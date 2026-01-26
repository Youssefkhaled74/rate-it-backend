# Vite Configuration Reference

## Current vite.config.js (Already Correct ✅)

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
```

**Status:** ✅ This is correct. No changes needed.

---

## What Each Part Does

### Laravel Vite Plugin Configuration
```javascript
laravel({
    input: ['resources/css/app.css', 'resources/js/app.js'],
    refresh: true,
})
```

- **input:** Entry points for Vite to process
  - `resources/css/app.css` - Compiles Tailwind
  - `resources/js/app.js` - Bundles Alpine.js and other JS
  
- **refresh: true** - Enables HMR (Hot Module Reload)
  - Changes to Blade files auto-refresh browser
  - Changes to CSS/JS auto-reload without page refresh

### Tailwind CSS Vite Plugin
```javascript
tailwindcss()
```

- Automatically processes `@import 'tailwindcss'`
- Scans @source paths for class extraction
- Uses Tailwind v4 features (@theme, @source, etc.)

### Server Configuration
```javascript
server: {
    watch: {
        ignored: ['**/storage/framework/views/**'],
    },
}
```

- Tells Vite to ignore compiled view cache
- Prevents unnecessary rebuilds

---

## package.json Scripts

Your `package.json` should have these scripts:

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview"
  }
}
```

### What Each Script Does

**npm run dev** - Start development server
```bash
npm run dev
# Output:
#   VITE v5.x.x  ready in XXX ms
#   ➜  Local:   http://localhost:5173/
#   ➜  press h to show help
```
- Watches for changes
- Serves assets from memory (fast!)
- Injects HMR client script
- **Keep this running while developing**

**npm run build** - Production build
```bash
npm run build
# Creates:
#   public/build/
#   ├── assets/
#   │   ├── app-[hash].css
#   │   ├── app-[hash].js
#   │   └── [other files]
#   └── manifest.json
```
- Minifies and hashes all assets
- Creates manifest for @vite helper
- Ready for production deployment

**npm run preview** - Test production build locally
```bash
npm run preview
# Serves the built version
# Use to test production build before deploying
```

---

## Tailwind v4 Features Used

### @import 'tailwindcss'
```css
@import 'tailwindcss';
```
- Single import (replaces multiple imports in v3)
- Automatically includes base, components, utilities

### @source
```css
@source '../**/*.blade.php';
@source '../../Modules/**/resources/views/**/*.blade.php';
```
- Tells Tailwind where to scan for classes
- Vite makes this work with proper path resolution

### @theme
```css
@theme {
    --font-sans: 'Instrument Sans', ...;
}
```
- Defines CSS custom properties
- Works with Tailwind's dynamic property system

---

## How Vite + Tailwind Works

### Development Flow
```
1. Browser requests http://localhost:8000/admin/login
2. Blade includes @vite(['resources/css/app.css', 'resources/js/app.js'])
3. @vite generates <script type="module" src="http://localhost:5173/resources/css/app.css">
4. Vite dev server (port 5173) processes the file:
   - app.css: @import 'tailwindcss' → processes @source → generates CSS
   - app.js: Imports Alpine.js → bundles it
5. Browser receives compiled CSS with:
   - All Tailwind classes (scanned from Modules/)
   - CSS variables from auth-theme.css
   - Bundle includes Alpine.js
6. JavaScript runs:
   - Alpine.start() processes x-data directives
   - Theme toggle works
   - Translations display correctly
```

### Production Flow
```
1. npm run build creates public/build/ with hashed files
2. Blade includes @vite([...])
3. @vite() reads public/build/manifest.json
4. Manifest maps 'resources/css/app.css' → 'assets/app-abc123.css'
5. Browser receives optimized, minified assets
6. Same result as development, but faster!
```

---

## Environment Variables for Vite

If you need to configure Vite behavior, create a `.env.local` file:

```bash
VITE_API_BASE_URL=http://localhost:8000/admin/api
VITE_ADMIN_THEME=dark
```

Then access in JavaScript:
```javascript
const baseUrl = import.meta.env.VITE_API_BASE_URL;
const theme = import.meta.env.VITE_ADMIN_THEME;
```

Or in Blade:
```blade
<!-- Not directly accessible in Blade, use data attributes instead -->
<div data-api-url="{{ env('VITE_API_BASE_URL') }}">
```

---

## Troubleshooting Vite Issues

### Issue: "localhost:5173 is refused"
**Solution:** Vite dev server isn't running
```bash
npm run dev
# Keep this terminal open
```

### Issue: CSS not updating in browser
**Solution:** Vite HMR not working
- Make sure Vite is running
- Check browser console (F12) for errors
- Hard refresh: Ctrl+Shift+R (or Cmd+Shift+R on Mac)

### Issue: "Can't resolve '@tailwindcss/vite'"
**Solution:** Dependencies not installed
```bash
npm install
```

### Issue: Tailwind classes still not applying
**Solution:** Missing @source path
- Check if @source lines include `../../Modules/`
- Add path if missing: `@source '../../Modules/**/resources/views/**/*.blade.php';`

### Issue: "Cannot find module 'alpinejs'"
**Solution:** Alpine not installed
```bash
npm install
```

---

## Next Steps

1. **Install dependencies:**
   ```bash
   npm install
   ```

2. **Start Vite server:**
   ```bash
   npm run dev
   # Keep this running
   ```

3. **In another terminal, clear Laravel caches:**
   ```bash
   php artisan cache:clear
   php artisan view:clear
   ```

4. **Visit login page:**
   ```
   http://127.0.0.1:8000/admin/login
   ```

5. **Check browser DevTools (F12):**
   - Network tab: Assets load (200 status)
   - Console: No errors
   - Elements: Styles are applied (border-radius, colors, shadows)

✅ **Done!** You should see a styled login form with proper Tailwind styling and translations.

