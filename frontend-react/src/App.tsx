import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { ShellLayout } from './components/ShellLayout';
import { Home } from './pages/Home';
import { Health } from './pages/Health';
import { ApiTest } from './pages/ApiTest';
import { Projects } from './pages/Projects';
import { Calendar } from './pages/Calendar';
import { Tickets } from './pages/Tickets';
import { Search } from './pages/Search';
import { Files } from './pages/Files';
import { Tags } from './pages/Tags';
import { Users } from './pages/admin/Users';
import { Roles } from './pages/admin/Roles';
import { Modules } from './pages/admin/Modules';
import { Settings } from './pages/admin/Settings';
import { UserSettingsPage } from './features/settings/UserSettingsPage';
import { TimecardDayPage } from './features/timecard/TimecardDayPage';
import { SimpleTest } from './pages/SimpleTest';
import './App.css';

/**
 * Main Application Component
 *
 * Sets up React Router with the following routes:
 *
 * Main Modules:
 * - / : Dashboard (Home page)
 * - /projects : Projects module
 * - /calendar : Calendar module
 * - /timecard : Timecard module
 * - /tickets : Tickets module
 *
 * Tools:
 * - /search : Search functionality
 * - /files : File management
 * - /tags : Tag management
 *
 * Administration:
 * - /admin/users : User management
 * - /admin/roles : Role management
 * - /admin/modules : Module management
 * - /admin/settings : Application settings
 *
 * Development:
 * - /health : Health check page
 * - /api-test : API client test page
 *
 * All routes use the ShellLayout wrapper for consistent navigation.
 *
 * Note: basename is set to /app for production (when served from PHP backend at /app/)
 * but empty for development (Vite dev server at root)
 */
function App() {
  // Use /app basename in production, root in development
  const basename = import.meta.env.MODE === 'production' ? '/app' : '';

  return (
    <BrowserRouter basename={basename}>
      <Routes>
        <Route element={<ShellLayout />}>
          {/* Dashboard */}
          <Route index element={<Home />} />

          {/* Main Modules */}
          <Route path="projects" element={<Projects />} />
          <Route path="calendar" element={<Calendar />} />
          <Route path="timecard" element={<TimecardDayPage />} />
          <Route path="tickets" element={<Tickets />} />

          {/* Tools */}
          <Route path="search" element={<Search />} />
          <Route path="files" element={<Files />} />
          <Route path="tags" element={<Tags />} />

          {/* User Settings (Module A - First vertical slice) */}
          <Route path="settings" element={<UserSettingsPage />} />
          <Route path="settings/:moduleName" element={<UserSettingsPage />} />

          {/* Administration */}
          <Route path="admin/users" element={<Users />} />
          <Route path="admin/roles" element={<Roles />} />
          <Route path="admin/modules" element={<Modules />} />
          <Route path="admin/settings" element={<Settings />} />

          {/* Development Tools */}
          <Route path="health" element={<Health />} />
          <Route path="api-test" element={<ApiTest />} />
          <Route path="test" element={<SimpleTest />} />

          {/* Fallback route - redirect to home */}
          <Route path="*" element={<Navigate to="/" replace />} />
        </Route>
      </Routes>
    </BrowserRouter>
  );
}

export default App;
