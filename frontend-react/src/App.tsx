import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { ShellLayout } from './components/ShellLayout';
import { Home } from './pages/Home';
import { Health } from './pages/Health';
import './App.css';

/**
 * Main Application Component
 *
 * Sets up React Router with the following routes:
 * - / : Home page (landing/dashboard)
 * - /health : Health check page
 *
 * All routes use the ShellLayout wrapper for consistent navigation.
 */
function App() {
  return (
    <BrowserRouter basename="/app">
      <Routes>
        <Route element={<ShellLayout />}>
          <Route index element={<Home />} />
          <Route path="health" element={<Health />} />

          {/* Fallback route - redirect to home */}
          <Route path="*" element={<Navigate to="/" replace />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}

export default App;
