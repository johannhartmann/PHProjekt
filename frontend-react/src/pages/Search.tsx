import { ModuleStub } from '../components/ModuleStub';

export function Search() {
  return (
    <ModuleStub
      icon="🔍"
      title="Search"
      description="Full-text search across all modules and content types. Quickly find projects, events, tickets, and other data with powerful search capabilities."
      features={[
        'Full-text search across all modules',
        'Advanced filtering by module type',
        'Search result highlighting',
        'Quick preview of results',
        'Recent searches history',
        'Saved search queries',
        'Search within specific projects',
        'Fuzzy matching support',
      ]}
      dojoPath="/index.php#Search"
    />
  );
}
