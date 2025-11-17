import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { api } from '@/api';
import type { SettingModule, SettingDetailResponse } from '@/api';
import { SettingsForm } from './SettingsForm';
import './UserSettingsPage.css';

/**
 * User Settings Page
 *
 * Main settings page with tab navigation for different setting modules.
 * Loads available modules and displays the appropriate settings form.
 */
export function UserSettingsPage() {
  const { moduleName } = useParams<{ moduleName?: string }>();
  const navigate = useNavigate();

  const [modules, setModules] = useState<SettingModule[]>([]);
  const [selectedModule, setSelectedModule] = useState<string>('User');
  const [settingsData, setSettingsData] = useState<SettingDetailResponse | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  // Load available setting modules
  useEffect(() => {
    let mounted = true;

    async function loadModules() {
      try {
        const data = await api.settings.getModules();
        if (mounted) {
          setModules(data);
          // Set selected module from URL or default to 'User'
          const initialModule = moduleName || 'User';
          if (data.some(m => m.name === initialModule)) {
            setSelectedModule(initialModule);
          }
        }
      } catch (err) {
        if (mounted) {
          setError(err instanceof Error ? err.message : 'Failed to load settings modules');
        }
      }
    }

    loadModules();

    return () => {
      mounted = false;
    };
  }, [moduleName]);

  // Load settings for selected module
  useEffect(() => {
    let mounted = true;

    async function loadSettings() {
      setLoading(true);
      setError(null);

      try {
        const data = await api.settings.getModuleSettings(selectedModule);
        if (mounted) {
          setSettingsData(data);
        }
      } catch (err) {
        if (mounted) {
          setError(err instanceof Error ? err.message : 'Failed to load settings');
        }
      } finally {
        if (mounted) {
          setLoading(false);
        }
      }
    }

    if (selectedModule) {
      loadSettings();
    }

    return () => {
      mounted = false;
    };
  }, [selectedModule]);

  const handleModuleChange = (module: string) => {
    setSelectedModule(module);
    navigate(`/settings/${module}`);
  };

  const handleSaveSuccess = () => {
    // Reload settings to get updated values
    setSettingsData(null);
    setLoading(true);
  };

  if (modules.length === 0 && !error) {
    return (
      <div className="settings-page">
        <div className="settings-loading">Loading settings...</div>
      </div>
    );
  }

  return (
    <div className="settings-page">
      <div className="settings-header">
        <h1>Settings</h1>
        <p className="settings-description">
          Configure your personal preferences and application settings.
        </p>
      </div>

      {error && (
        <div className="settings-error">
          <strong>Error:</strong> {error}
        </div>
      )}

      <div className="settings-container">
        {/* Module Tabs */}
        <div className="settings-tabs">
          <h2 className="settings-tabs-title">Setting Modules</h2>
          <div className="settings-tabs-list">
            {modules.map((module) => (
              <button
                key={module.name}
                className={`settings-tab ${
                  selectedModule === module.name ? 'active' : ''
                }`}
                onClick={() => handleModuleChange(module.name)}
                type="button"
              >
                {module.label}
              </button>
            ))}
          </div>
        </div>

        {/* Settings Form */}
        <div className="settings-content">
          {loading ? (
            <div className="settings-loading">
              <div className="settings-spinner"></div>
              <p>Loading {selectedModule} settings...</p>
            </div>
          ) : settingsData ? (
            <SettingsForm
              moduleName={selectedModule}
              metadata={settingsData.metadata}
              data={settingsData.data[0] || {}}
              onSaveSuccess={handleSaveSuccess}
            />
          ) : (
            <div className="settings-empty">
              No settings available for {selectedModule}.
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
