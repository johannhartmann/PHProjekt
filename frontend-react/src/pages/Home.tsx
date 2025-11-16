import './Home.css';

/**
 * Home Page
 *
 * Landing page for the React SPA.
 * This will eventually serve as the main dashboard.
 */
export function Home() {
  return (
    <div className="home-page">
      <div className="home-hero">
        <h1>Welcome to PHProjekt React Frontend</h1>
        <p className="home-subtitle">
          Modern React SPA built with TypeScript and Vite
        </p>
      </div>

      <div className="home-cards">
        <div className="home-card">
          <h2>🚀 Status</h2>
          <p>React frontend successfully initialized and running</p>
          <ul>
            <li>✅ Vite build system</li>
            <li>✅ TypeScript configured</li>
            <li>✅ React Router setup</li>
            <li>✅ ShellLayout component</li>
          </ul>
        </div>

        <div className="home-card">
          <h2>📋 Migration Strategy</h2>
          <p>Using the strangler pattern to gradually migrate from Dojo to React</p>
          <ul>
            <li>Coexistence with legacy Dojo frontend</li>
            <li>Module-by-module migration</li>
            <li>Shared backend API</li>
            <li>React app served from /react/</li>
          </ul>
        </div>

        <div className="home-card">
          <h2>🎯 Next Steps</h2>
          <p>Ready to start migrating modules</p>
          <ul>
            <li>Set up API client (Axios)</li>
            <li>Configure i18n (i18next)</li>
            <li>Implement authentication</li>
            <li>Migrate first module (Timecard or Project)</li>
          </ul>
        </div>
      </div>

      <div className="home-info">
        <h2>Environment Information</h2>
        <div className="info-grid">
          <div className="info-item">
            <strong>Mode:</strong> {import.meta.env.MODE}
          </div>
          <div className="info-item">
            <strong>Base URL:</strong> {import.meta.env.BASE_URL}
          </div>
          <div className="info-item">
            <strong>Dev:</strong> {import.meta.env.DEV ? 'Yes' : 'No'}
          </div>
          <div className="info-item">
            <strong>Prod:</strong> {import.meta.env.PROD ? 'Yes' : 'No'}
          </div>
        </div>
      </div>
    </div>
  );
}
