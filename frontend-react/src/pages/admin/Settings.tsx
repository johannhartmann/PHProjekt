import { ModuleStub } from '../../components/ModuleStub';

export function Settings() {
  return (
    <ModuleStub
      icon="⚙️"
      title="Settings"
      description="Application-wide settings and configuration. Manage system preferences, user settings, and global parameters."
      features={[
        'User preferences and personal settings',
        'Language and localization settings',
        'Time zone configuration',
        'Date/time format preferences',
        'Email notification settings',
        'Theme and appearance options',
        'Default module on login',
        'Session timeout configuration',
        'Calendar settings (week start, working hours)',
        'Export/import preferences',
      ]}
      dojoPath="/index.php#Core/Setting"
    />
  );
}
