import { ModuleStub } from '../../components/ModuleStub';

export function Roles() {
  return (
    <ModuleStub
      icon="🔐"
      title="Role Management"
      description="Define and manage roles with granular permissions. Control access to modules, features, and data based on user roles."
      features={[
        'Role creation and editing',
        'Module-level permissions matrix',
        'Feature-level access control',
        'Read/Write/Admin permission levels',
        'Role assignment to users',
        'Role hierarchies',
        'Permission inheritance',
        'Audit trail for role changes',
        'Default role templates',
      ]}
      dojoPath="/index.php#Core/Role"
    />
  );
}
