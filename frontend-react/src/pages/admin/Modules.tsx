import { ModuleStub } from '../../components/ModuleStub';

export function Modules() {
  return (
    <ModuleStub
      icon="🧩"
      title="Module Management"
      description="Configure and customize modules throughout PHProjekt. Use the module designer to create custom fields and adapt modules to your workflow."
      features={[
        'Module activation/deactivation',
        'Module designer (drag & drop field builder)',
        'Custom field creation',
        'Field type configuration (text, date, select, etc.)',
        'Required field settings',
        'Field ordering and grouping',
        'Module-specific settings',
        'Field validation rules',
        'Default value configuration',
        'Module export/import',
      ]}
      dojoPath="/index.php#Core/Module"
    />
  );
}
