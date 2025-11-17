import { ModuleStub } from '../components/ModuleStub';

export function Tags() {
  return (
    <ModuleStub
      icon="🏷️"
      title="Tags"
      description="Tag management system for organizing and categorizing content across all modules. Create, manage, and analyze tag usage throughout your organization."
      features={[
        'Create and manage tags globally',
        'Tag usage analytics',
        'Cross-module tag search',
        'Tag hierarchies and relationships',
        'Tag color coding',
        'Auto-suggestion for existing tags',
        'Tag merging and cleanup tools',
        'Most used tags dashboard',
      ]}
      dojoPath="/index.php#Tag"
    />
  );
}
