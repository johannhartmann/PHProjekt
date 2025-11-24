# Playwright Browser Crash Issue

**Date**: 2025-11-24
**Status**: CRITICAL - UNRESOLVED
**Affects**: All E2E tests (23/23 failing)

## Summary

All Playwright E2E tests crash with `Navigation failed because page crashed!` error. This is a **browser-level crash**, not a JavaScript error. Extensive debugging has identified this as a compatibility issue between Playwright's Chromium and the Vite dev server environment.

## Error Pattern

```
Error: page.waitForLoadState: Navigation failed because page crashed!
```

This error occurs consistently across all tests, even with the most minimal React component.

## Root Cause Investigation

### What Was Tested

1. **React Version**: Tested both React 18.3.1 and React 19.2.0 - crashes with both ✅
2. **React Router**: Removed React Router entirely - still crashes ✅
3. **StrictMode**: Disabled StrictMode - still crashes ✅
4. **Component Complexity**: Tested minimal single-div component - still crashes ✅
5. **Memory**: 12GB available, not a memory issue ✅
6. **System Logs**: No kernel crashes or OOM kills ✅

### Timeline of Browser Behavior

From console logs during test execution:

```
1. Navigating to /test...
2. [Browser debug]: [vite] connecting...
3. [Browser debug]: [vite] connected.
4. [Browser info]: React DevTools message appears
5. Waiting for page load...
6. **CRASH** - Navigation failed because page crashed!
```

**Key Observation**: The crash occurs AFTER:
- Vite successfully connects
- React starts loading (DevTools message appears)
- But BEFORE React completes first render

### Conclusion

This is a **Playwright + Vite + React interaction issue**, likely related to:
1. Vite's HMR (Hot Module Replacement) WebSocket connection
2. React Refresh plugin's interaction with Playwright's Chromium
3. Possible Playwright version incompatibility with current Vite/React versions

## Technical Details

**Environment**:
- Playwright: 1.56.1
- Vite: 7.2.4
- React: 19.2.0 (also tested 18.3.1)
- React Router: 7.9.6
- Node: Latest in Docker container
- Chromium: Playwright's bundled version

**Minimal Reproducible Example**:

```typescript
// App.tsx - This crashes Playwright!
export default function App() {
  return <div><h1>Test</h1></div>;
}
```

```typescript
// Test - simple-load.spec.ts
test('load page', async ({ page }) => {
  await page.goto('/test');
  await page.waitForLoadState('networkidle'); // CRASHES HERE
});
```

## Known Workarounds

### Option 1: Use Production Build (UNTESTED)

Instead of Vite dev server, test against production build:

```javascript
// playwright.config.ts
webServer: {
  command: 'npm run preview',  // Instead of 'npm run dev'
  url: 'http://localhost:4173',
}
```

**Pros**: Avoids Vite HMR, might prevent crash
**Cons**: Slower iteration, no source maps, different from dev environment

### Option 2: Downgrade Playwright (UNTESTED)

Try older Playwright version that may be compatible:

```bash
npm install -D @playwright/test@1.40.0
```

### Option 3: Alternative E2E Framework

Consider switching to:
- **Cypress**: Well-tested with Vite
- **Testing Library + jsdom**: No real browser, faster
- **Puppeteer**: Different Chromium automation

### Option 4: Wait for Fix

This may be a known issue. Check:
- Playwright GitHub: https://github.com/microsoft/playwright/issues
- Vite GitHub: https://github.com/vitejs/vite/issues
- Search: "playwright vite crash" or "playwright react 19 crash"

## Immediate Next Steps

1. **Search for Known Issues**:
   ```bash
   # Check if this is already reported
   gh issue list --repo microsoft/playwright --search "vite crash"
   gh issue list --repo vitejs/vite --search "playwright crash"
   ```

2. **Test Production Build**:
   ```bash
   npm run build
   npm run preview
   # Update playwright.config.ts to use preview server
   npx playwright test
   ```

3. **Try Playwright 1.40.x**:
   ```bash
   npm install -D @playwright/test@1.40.0
   npx playwright install chromium
   npx playwright test
   ```

4. **Test with Cypress** (if above fail):
   ```bash
   npm install -D cypress
   npx cypress open
   ```

## Files Created During Investigation

- `frontend-react/tests/e2e/simple-load.spec.ts` - Debug test with logging
- `frontend-react/tests/e2e/debug.spec.ts` - Screenshot capture test
- `frontend-react/tests/e2e/preview-test.spec.ts` - Production build test
- `frontend-react/src/AppMinimal.tsx` - Minimal app without Router
- `frontend-react/src/pages/SimpleTest.tsx` - Simple test page

These can be deleted once issue is resolved.

## Impact

- **Unit Tests**: ✅ 92/92 passing (not affected)
- **E2E Tests**: ❌ 0/23 passing (all crash)
- **Module A (Settings)**: Unable to verify with E2E tests
- **Module B (Timecard)**: Unable to verify with E2E tests

## Recommendation

**SHORT TERM**: Use manual testing for E2E validation until crash is resolved.

**MEDIUM TERM**: Test production build with Playwright or switch to Cypress.

**LONG TERM**: Monitor Playwright/Vite issues for official fix or upgrade path.

---

**Last Updated**: 2025-11-24
**Investigated By**: Claude Code (Anthropic)
