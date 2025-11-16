import { Outlet, Link } from 'react-router-dom';
import './ShellLayout.css';

/**
 * ShellLayout Component
 *
 * Main application layout component that provides:
 * - Top navigation bar
 * - Logo/branding
 * - Main navigation links
 * - Content area for routes
 *
 * This layout will be used across all React routes and will eventually
 * include the full PHProjekt navigation when modules are migrated.
 */
export function ShellLayout() {
  return (
    <div className="shell-layout">
      {/* Header / Navigation */}
      <header className="shell-header">
        <div className="shell-header-content">
          <div className="shell-logo">
            <h1>PHProjekt</h1>
            <span className="shell-badge">React SPA</span>
          </div>

          <nav className="shell-nav">
            <Link to="/" className="shell-nav-link">
              Home
            </Link>
            <Link to="/health" className="shell-nav-link">
              Health
            </Link>
            {/* Future navigation links will be added here as modules are migrated */}
          </nav>

          <div className="shell-user">
            <span>User Menu</span>
          </div>
        </div>
      </header>

      {/* Main Content Area */}
      <main className="shell-main">
        <div className="shell-content">
          <Outlet />
        </div>
      </main>

      {/* Footer */}
      <footer className="shell-footer">
        <p>PHProjekt 6 - React Frontend Migration</p>
      </footer>
    </div>
  );
}
