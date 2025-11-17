import { ModuleStub } from '../components/ModuleStub';

export function Projects() {
  return (
    <ModuleStub
      icon="📁"
      title="Projects"
      description="Manage your projects with hierarchical structure, permissions, and collaborative features. The Projects module is the core organizational unit in PHProjekt."
      features={[
        'Hierarchical project tree with unlimited nesting',
        'Project-level permissions and access control',
        'Tag support for categorization',
        'WebDAV integration for file management',
        'Submodule activation per project',
        'Role-based user assignments',
        'Project metadata and custom fields',
      ]}
      dojoPath="/index.php#Project"
    />
  );
}
