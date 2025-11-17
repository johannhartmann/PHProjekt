import { Outlet } from 'react-router-dom';
import { TopNav } from './TopNav';
import { SideNav } from './SideNav';
import './ShellLayout.css';

/**
 * ShellLayout Component
 *
 * Main application layout component that provides:
 * - Top navigation bar with user menu and language selector
 * - Side navigation menu with module links
 * - Content area for routes
 *
 * This layout is used across all React routes and implements
 * the full PHProjekt navigation structure.
 */
export function ShellLayout() {
  return (
    <div className="shell-layout">
      {/* Top Navigation */}
      <TopNav />

      {/* Main Container: Side Nav + Content */}
      <div className="shell-container">
        {/* Side Navigation */}
        <SideNav />

        {/* Main Content Area */}
        <main className="shell-main">
          <div className="shell-content">
            <Outlet />
          </div>
        </main>
      </div>
    </div>
  );
}
