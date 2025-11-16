import { useState, useEffect } from 'react';
import './Health.css';

/**
 * Health Check Page
 *
 * Simple page to verify the React app is running correctly.
 * Shows system status and basic health information.
 */
export function Health() {
  const [timestamp, setTimestamp] = useState(new Date().toISOString());

  useEffect(() => {
    const interval = setInterval(() => {
      setTimestamp(new Date().toISOString());
    }, 1000);

    return () => clearInterval(interval);
  }, []);

  const healthData = {
    status: 'OK',
    version: '1.0.0',
    environment: import.meta.env.MODE,
    timestamp,
    uptime: performance.now(),
    features: {
      reactRouter: true,
      typescript: true,
      vite: true,
      cssModules: false,
    },
  };

  return (
    <div className="health-page">
      <div className="health-header">
        <div className="health-status-badge health-status-ok">
          ✓ Healthy
        </div>
        <h1>System Health Check</h1>
        <p>React frontend is running normally</p>
      </div>

      <div className="health-grid">
        <div className="health-card">
          <h2>Status</h2>
          <div className="health-value">
            <span className="status-indicator status-ok"></span>
            {healthData.status}
          </div>
        </div>

        <div className="health-card">
          <h2>Version</h2>
          <div className="health-value">{healthData.version}</div>
        </div>

        <div className="health-card">
          <h2>Environment</h2>
          <div className="health-value">{healthData.environment}</div>
        </div>

        <div className="health-card">
          <h2>Uptime</h2>
          <div className="health-value">
            {(healthData.uptime / 1000).toFixed(2)}s
          </div>
        </div>
      </div>

      <div className="health-details">
        <h2>Current Timestamp</h2>
        <div className="timestamp">{timestamp}</div>
      </div>

      <div className="health-details">
        <h2>Feature Flags</h2>
        <div className="feature-list">
          {Object.entries(healthData.features).map(([feature, enabled]) => (
            <div key={feature} className="feature-item">
              <span className={`feature-status ${enabled ? 'enabled' : 'disabled'}`}>
                {enabled ? '✓' : '✗'}
              </span>
              <span className="feature-name">{feature}</span>
            </div>
          ))}
        </div>
      </div>

      <div className="health-details">
        <h2>Full Health Data</h2>
        <pre className="health-json">
          {JSON.stringify(healthData, null, 2)}
        </pre>
      </div>
    </div>
  );
}
