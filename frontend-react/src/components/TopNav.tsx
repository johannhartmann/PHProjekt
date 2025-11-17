import { useState } from 'react';
import { Link } from 'react-router-dom';
import './TopNav.css';

/**
 * TopNav Component
 *
 * Top navigation bar with:
 * - PHProjekt branding/logo
 * - User menu (dropdown)
 * - Language selector (dropdown)
 * - Notifications icon
 */
export function TopNav() {
  const [userMenuOpen, setUserMenuOpen] = useState(false);
  const [langMenuOpen, setLangMenuOpen] = useState(false);

  // Placeholder user data - will be replaced with real data from API
  const currentUser = {
    name: 'Demo User',
    email: 'demo@phprojekt.com',
  };

  const currentLanguage = 'en';

  const languages = [
    { code: 'en', name: 'English' },
    { code: 'de', name: 'Deutsch' },
    { code: 'es', name: 'Español' },
    { code: 'fr', name: 'Français' },
  ];

  const handleLogout = () => {
    // TODO: Implement proper logout via API
    window.location.href = '/index.php/Login/logout';
  };

  return (
    <header className="top-nav">
      <div className="top-nav-content">
        {/* Logo / Brand */}
        <div className="top-nav-brand">
          <Link to="/" className="top-nav-logo">
            <h1>PHProjekt</h1>
            <span className="top-nav-version">6.0</span>
          </Link>
        </div>

        {/* Right side actions */}
        <div className="top-nav-actions">
          {/* Language Selector */}
          <div
            className="top-nav-dropdown"
            onMouseEnter={() => setLangMenuOpen(true)}
            onMouseLeave={() => setLangMenuOpen(false)}
          >
            <button className="top-nav-button" aria-label="Select Language">
              <span className="top-nav-icon">🌐</span>
              <span className="top-nav-text">
                {languages.find((l) => l.code === currentLanguage)?.code.toUpperCase()}
              </span>
            </button>
            {langMenuOpen && (
              <div className="top-nav-dropdown-menu">
                {languages.map((lang) => (
                  <button
                    key={lang.code}
                    className={`top-nav-dropdown-item ${
                      lang.code === currentLanguage ? 'active' : ''
                    }`}
                    onClick={() => {
                      // TODO: Implement language switching
                      console.log('Switch to', lang.code);
                      setLangMenuOpen(false);
                    }}
                  >
                    {lang.name}
                  </button>
                ))}
              </div>
            )}
          </div>

          {/* Notifications (placeholder) */}
          <button className="top-nav-button" aria-label="Notifications">
            <span className="top-nav-icon">🔔</span>
            <span className="top-nav-badge">0</span>
          </button>

          {/* User Menu */}
          <div
            className="top-nav-dropdown"
            onMouseEnter={() => setUserMenuOpen(true)}
            onMouseLeave={() => setUserMenuOpen(false)}
          >
            <button className="top-nav-button top-nav-user" aria-label="User Menu">
              <span className="top-nav-avatar">
                {currentUser.name
                  .split(' ')
                  .map((n) => n[0])
                  .join('')
                  .toUpperCase()}
              </span>
              <span className="top-nav-text">{currentUser.name}</span>
              <span className="top-nav-caret">▼</span>
            </button>
            {userMenuOpen && (
              <div className="top-nav-dropdown-menu">
                <div className="top-nav-user-info">
                  <div className="top-nav-user-name">{currentUser.name}</div>
                  <div className="top-nav-user-email">{currentUser.email}</div>
                </div>
                <div className="top-nav-dropdown-divider"></div>
                <Link
                  to="/profile"
                  className="top-nav-dropdown-item"
                  onClick={() => setUserMenuOpen(false)}
                >
                  👤 Profile
                </Link>
                <Link
                  to="/admin/settings"
                  className="top-nav-dropdown-item"
                  onClick={() => setUserMenuOpen(false)}
                >
                  ⚙️ Settings
                </Link>
                <div className="top-nav-dropdown-divider"></div>
                <button className="top-nav-dropdown-item" onClick={handleLogout}>
                  🚪 Logout
                </button>
              </div>
            )}
          </div>
        </div>
      </div>
    </header>
  );
}
