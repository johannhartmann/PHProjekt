import { ModuleStub } from '../../components/ModuleStub';

export function Users() {
  return (
    <ModuleStub
      icon="👥"
      title="User Management"
      description="Comprehensive user administration. Create, edit, and manage user accounts, permissions, and access rights across the organization."
      features={[
        'User account creation and management',
        'User profile editing',
        'Password reset and management',
        'Active/inactive user status',
        'Project assignment per user',
        'Role assignment and permissions',
        'Proxy user configuration',
        'User activity monitoring',
        'Bulk user import/export',
        'Email notification settings',
      ]}
      dojoPath="/index.php#Core/User"
    />
  );
}
