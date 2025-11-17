import { useState } from 'react';
import { NavLink } from 'react-router-dom';
import './SideNav.css';

/**
 * SideNav Component
 *
 * Side navigation menu with:
 * - Main module links (Projects, Calendar, Timecard)
 * - Administration submenu (Users, Roles, Modules, Settings)
 * - Collapsible/expandable sections
 * - Active link highlighting
 */
export function SideNav() {
  const [adminExpanded, setAdminExpanded] = useState(false);
  const [collapsed, setCollapsed] = useState(false);

  return (
    <nav className={`side-nav ${collapsed ? 'collapsed' : ''}`}>
      {/* Collapse Toggle */}
      <button
        className="side-nav-toggle"
        onClick={() => setCollapsed(!collapsed)}
        aria-label={collapsed ? 'Expand menu' : 'Collapse menu'}
      >
        {collapsed ? '»' : '«'}
      </button>

      {/* Main Navigation */}
      <div className="side-nav-content">
        {/* Primary Modules */}
        <div className="side-nav-section">
          <NavLink to="/" className="side-nav-item" end>
            <span className="side-nav-icon">🏠</span>
            <span className="side-nav-label">Dashboard</span>
          </NavLink>

          <NavLink to="/projects" className="side-nav-item">
            <span className="side-nav-icon">📁</span>
            <span className="side-nav-label">Projects</span>
          </NavLink>

          <NavLink to="/calendar" className="side-nav-item">
            <span className="side-nav-icon">📅</span>
            <span className="side-nav-label">Calendar</span>
          </NavLink>

          <NavLink to="/timecard" className="side-nav-item">
            <span className="side-nav-icon">⏱️</span>
            <span className="side-nav-label">Timecard</span>
          </NavLink>

          <NavLink to="/tickets" className="side-nav-item">
            <span className="side-nav-icon">🎫</span>
            <span className="side-nav-label">Tickets</span>
          </NavLink>
        </div>

        {/* Divider */}
        <div className="side-nav-divider"></div>

        {/* Tools */}
        <div className="side-nav-section">
          <NavLink to="/search" className="side-nav-item">
            <span className="side-nav-icon">🔍</span>
            <span className="side-nav-label">Search</span>
          </NavLink>

          <NavLink to="/files" className="side-nav-item">
            <span className="side-nav-icon">📎</span>
            <span className="side-nav-label">Files</span>
          </NavLink>

          <NavLink to="/tags" className="side-nav-item">
            <span className="side-nav-icon">🏷️</span>
            <span className="side-nav-label">Tags</span>
          </NavLink>
        </div>

        {/* Divider */}
        <div className="side-nav-divider"></div>

        {/* User Settings */}
        <div className="side-nav-section">
          <NavLink to="/settings" className="side-nav-item">
            <span className="side-nav-icon">👤</span>
            <span className="side-nav-label">My Settings</span>
          </NavLink>
        </div>

        {/* Divider */}
        <div className="side-nav-divider"></div>

        {/* Administration (Collapsible) */}
        <div className="side-nav-section">
          <button
            className={`side-nav-item side-nav-expandable ${
              adminExpanded ? 'expanded' : ''
            }`}
            onClick={() => setAdminExpanded(!adminExpanded)}
          >
            <span className="side-nav-icon">⚙️</span>
            <span className="side-nav-label">Administration</span>
            <span className="side-nav-expand-icon">
              {adminExpanded ? '▼' : '▶'}
            </span>
          </button>

          {adminExpanded && (
            <div className="side-nav-submenu">
              <NavLink to="/admin/users" className="side-nav-subitem">
                <span className="side-nav-icon">👥</span>
                <span className="side-nav-label">Users</span>
              </NavLink>

              <NavLink to="/admin/roles" className="side-nav-subitem">
                <span className="side-nav-icon">🔐</span>
                <span className="side-nav-label">Roles</span>
              </NavLink>

              <NavLink to="/admin/modules" className="side-nav-subitem">
                <span className="side-nav-icon">🧩</span>
                <span className="side-nav-label">Modules</span>
              </NavLink>

              <NavLink to="/admin/settings" className="side-nav-subitem">
                <span className="side-nav-icon">⚙️</span>
                <span className="side-nav-label">Settings</span>
              </NavLink>
            </div>
          )}
        </div>

        {/* Divider */}
        <div className="side-nav-divider"></div>

        {/* Development Tools (only visible in dev) */}
        {import.meta.env.DEV && (
          <div className="side-nav-section">
            <div className="side-nav-section-title">
              <span className="side-nav-label">Development</span>
            </div>

            <NavLink to="/health" className="side-nav-item">
              <span className="side-nav-icon">🏥</span>
              <span className="side-nav-label">Health Check</span>
            </NavLink>

            <NavLink to="/api-test" className="side-nav-item">
              <span className="side-nav-icon">🧪</span>
              <span className="side-nav-label">API Test</span>
            </NavLink>
          </div>
        )}
      </div>
    </nav>
  );
}
