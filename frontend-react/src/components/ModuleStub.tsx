import './ModuleStub.css';

interface ModuleStubProps {
  icon: string;
  title: string;
  description: string;
  features?: string[];
  dojoPath?: string;
}

/**
 * ModuleStub Component
 *
 * Temporary placeholder page for modules during React migration.
 * Shows module information and links to legacy Dojo version.
 */
export function ModuleStub({
  icon,
  title,
  description,
  features = [],
  dojoPath,
}: ModuleStubProps) {
  return (
    <div className="module-stub">
      <div className="module-stub-header">
        <span className="module-stub-icon">{icon}</span>
        <div className="module-stub-title-group">
          <h1 className="module-stub-title">{title}</h1>
          <span className="module-stub-badge">React Migration - Coming Soon</span>
        </div>
      </div>

      <div className="module-stub-content">
        <p className="module-stub-description">{description}</p>

        {features.length > 0 && (
          <div className="module-stub-features">
            <h2>Planned Features</h2>
            <ul>
              {features.map((feature, index) => (
                <li key={index}>{feature}</li>
              ))}
            </ul>
          </div>
        )}

        {dojoPath && (
          <div className="module-stub-legacy">
            <h2>Need to use this module now?</h2>
            <p>
              The legacy Dojo version is still available while we complete the React
              migration.
            </p>
            <a
              href={dojoPath}
              className="module-stub-legacy-link"
              target="_blank"
              rel="noopener noreferrer"
            >
              Open Legacy {title} Module →
            </a>
          </div>
        )}
      </div>

      <div className="module-stub-footer">
        <div className="module-stub-status">
          <span className="module-stub-status-label">Migration Status:</span>
          <div className="module-stub-progress">
            <div className="module-stub-progress-bar" style={{ width: '5%' }}></div>
          </div>
          <span className="module-stub-status-text">5% - Planning Phase</span>
        </div>
      </div>
    </div>
  );
}
