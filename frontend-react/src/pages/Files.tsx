import { ModuleStub } from '../components/ModuleStub';

export function Files() {
  return (
    <ModuleStub
      icon="📎"
      title="Files"
      description="Centralized file management and storage. Upload, organize, and share files across projects with version control and access management."
      features={[
        'File upload and management',
        'WebDAV protocol support',
        'Version history tracking',
        'File preview capabilities',
        'Access control per file',
        'Bulk upload support',
        'File search and filtering',
        'Storage quota management',
        'Integration with projects and modules',
      ]}
      dojoPath="/index.php#File"
    />
  );
}
