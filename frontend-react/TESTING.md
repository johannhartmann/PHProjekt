# Testing Guide - React Navigation

This document provides manual testing steps to verify the React navigation implementation.

## Prerequisites

1. **Start the development server:**
   ```bash
   cd frontend-react
   npm run dev
   # Server runs on http://localhost:3000
   ```

2. **Or test the production build:**
   ```bash
   npm run build
   # Then start PHP server in phprojekt/htdocs
   cd ../phprojekt/htdocs
   php -S localhost:8080
   # Visit http://localhost:8080/app/
   ```

## React App Verification (/app routes)

### 1. Top Navigation
Navigate to: `http://localhost:3000/app/` (dev) or `http://localhost:8080/app/` (prod)

**Test:**
- [ ] PHProjekt logo and version (6.0) visible
- [ ] Language selector shows "EN"
- [ ] Hover over language selector → dropdown appears with EN, DE, ES, FR
- [ ] Notifications icon shows badge (0)
- [ ] User menu shows "Demo User"
- [ ] Hover over user menu → dropdown shows Profile, Settings, Logout
- [ ] No console errors

### 2. Side Navigation
**Test:**
- [ ] Sidebar visible on left side (260px wide)
- [ ] All module icons and labels visible:
  - Dashboard 🏠
  - Projects 📁
  - Calendar 📅
  - Timecard ⏱️
  - Tickets 🎫
  - Search 🔍
  - Files 📎
  - Tags 🏷️
  - Administration ⚙️
- [ ] Click collapse toggle (« button) → sidebar collapses to 70px
- [ ] Only icons visible when collapsed
- [ ] Click expand toggle (» button) → sidebar expands to 260px

### 3. Navigation - Main Modules
**Test each route:**

| Route | Action | Expected Result |
|-------|--------|----------------|
| `/app/` | Click "Dashboard" | Home page displays |
| `/app/projects` | Click "Projects" | Projects stub page shows |
| `/app/calendar` | Click "Calendar" | Calendar stub page shows |
| `/app/timecard` | Click "Timecard" | Timecard stub page shows |
| `/app/tickets` | Click "Tickets" | Tickets stub page shows |

**Verify for each:**
- [ ] URL updates correctly
- [ ] Active link highlighted with gradient background
- [ ] Page title and icon display
- [ ] "React Migration - Coming Soon" badge visible
- [ ] Feature list displays
- [ ] "Open Legacy X Module →" link present
- [ ] No console errors

### 4. Navigation - Tools
**Test:**
- [ ] `/app/search` → Search stub page
- [ ] `/app/files` → Files stub page
- [ ] `/app/tags` → Tags stub page

### 5. Navigation - Administration (Expandable)
**Test:**
- [ ] Click "Administration" button → submenu expands
- [ ] Submenu shows:
  - Users 👥
  - Roles 🔐
  - Modules 🧩
  - Settings ⚙️
- [ ] Click "Users" → `/app/admin/users` loads
- [ ] Click "Roles" → `/app/admin/roles` loads
- [ ] Click "Modules" → `/app/admin/modules` loads
- [ ] Click "Settings" → `/app/admin/settings` loads
- [ ] Click "Administration" again → submenu collapses

### 6. Development Tools (Dev mode only)
**Test (only visible in development):**
- [ ] Health Check link visible
- [ ] API Test link visible
- [ ] `/app/health` → Health page loads
- [ ] `/app/api-test` → API test page loads

### 7. Mobile Responsiveness
**Test (resize browser to < 768px):**
- [ ] Top nav logo text becomes smaller
- [ ] User name and language text hidden on mobile
- [ ] Sidebar behavior appropriate
- [ ] Touch-friendly navigation

### 8. Direct URL Navigation
**Test by manually entering URLs:**
- [ ] `http://localhost:3000/app/projects` loads directly
- [ ] `http://localhost:3000/app/calendar` loads directly
- [ ] `http://localhost:3000/app/admin/users` loads directly
- [ ] `http://localhost:3000/app/invalid-route` redirects to home
- [ ] Browser back/forward buttons work correctly

## Dojo Layout Verification (Legacy Routes)

### 1. Main Dojo App
Navigate to: `http://localhost:8080/index.php`

**Test:**
- [ ] Dojo app loads without errors
- [ ] Old layout still intact
- [ ] No interference from React code
- [ ] Console shows no errors related to React

### 2. Dojo Module Routes
**Test existing Dojo modules:**
- [ ] `http://localhost:8080/index.php#Project` → Projects module loads
- [ ] `http://localhost:8080/index.php#Calendar2` → Calendar loads
- [ ] `http://localhost:8080/index.php#Timecard` → Timecard loads
- [ ] All functionality works as before

### 3. Coexistence
**Test both apps running:**
- [ ] Open React app in one browser tab: `/app/`
- [ ] Open Dojo app in another tab: `/index.php`
- [ ] Both work independently
- [ ] No cross-contamination of styles
- [ ] No JavaScript errors in either

## Known Issues / Expected Behavior

### React App (Expected)
- ✅ All module pages show stubs (not implemented yet)
- ✅ "Open Legacy Module" links point to Dojo app
- ✅ User menu actions are placeholders
- ✅ Language selector doesn't change language yet
- ✅ No authentication implemented yet

### Dojo App (Should remain unchanged)
- ✅ All existing functionality intact
- ✅ No visual changes
- ✅ No new errors introduced

## Console Verification

### React App Console (Should be clean)
Open Developer Tools → Console

**Expected:**
- No errors
- No warnings (except potential dev-mode warnings from React Router)
- Source maps load correctly

**Common acceptable warnings:**
- React Router dev warnings
- React strict mode warnings (dev only)

### Dojo App Console (Should be unchanged)
**Expected:**
- Same warnings/errors as before migration
- No new React-related errors

## Performance Check

### React App
**Test:**
- [ ] Initial page load < 2 seconds
- [ ] Navigation between routes is instant
- [ ] No layout shift during load
- [ ] Smooth animations (sidebar collapse, dropdown menus)

### Bundle Size
**Check (in build output):**
```bash
npm run build
```

**Expected:**
- index.js: ~250 kB (gzipped ~80 kB)
- index.css: ~15 kB (gzipped ~3.5 kB)
- Total < 300 kB

## Accessibility Check

### Keyboard Navigation
**Test:**
- [ ] Tab key navigates through all links
- [ ] Enter key activates links
- [ ] Focus visible on all interactive elements
- [ ] Skip to content link (if implemented)

### Screen Reader
**Test with screen reader (NVDA/JAWS/VoiceOver):**
- [ ] Navigation landmarks announced
- [ ] Links have proper labels
- [ ] Current page announced
- [ ] Dropdown menus accessible

## Cross-Browser Testing

**Test in:**
- [ ] Chrome/Chromium (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest, if available)
- [ ] Edge (latest)

## Test Results

**Date:** ___________
**Tester:** ___________
**Environment:** [ ] Dev [ ] Production
**Result:** [ ] PASS [ ] FAIL

**Notes:**
```
(Add any issues found or observations here)
```

---

## Automated Tests

**Run unit tests:**
```bash
cd frontend-react
npm test -- --run
```

**Expected:** All 53 tests pass
- 9 API type tests
- 29 API client tests
- 8 ShellLayout tests
- 7 Navigation tests

**Run build:**
```bash
npm run build
```

**Expected:** Build succeeds with no errors

---

## Manual Test Checklist Summary

- [ ] All automated tests pass
- [ ] Build succeeds
- [ ] React app loads at /app
- [ ] All navigation links work
- [ ] URLs update correctly
- [ ] Active links highlighted
- [ ] Sidebar collapse/expand works
- [ ] Admin submenu expands/collapses
- [ ] Dropdowns work (user menu, language)
- [ ] Mobile responsive
- [ ] Dojo app still works at /index.php
- [ ] No console errors
- [ ] No style conflicts between React and Dojo
- [ ] Performance acceptable
- [ ] Accessibility requirements met

**All items checked = Ready for commit! ✅**
