import { ModuleStub } from '../components/ModuleStub';

export function Calendar() {
  return (
    <ModuleStub
      icon="📅"
      title="Calendar"
      description="Comprehensive calendar system with multiple views, recurring events, and multi-user scheduling. Manage your time and coordinate with your team effectively."
      features={[
        'Multiple calendar views (month, week, day, multi-user)',
        'Recurring events with flexible patterns',
        'Drag & drop event management',
        'Event resizing and quick edits',
        'Availability checking for meeting scheduling',
        'Multi-user calendar overlay',
        'CalDAV protocol support for external clients',
        'CSV export for reporting',
        'Tag-based event categorization',
      ]}
      dojoPath="/index.php#Calendar2"
    />
  );
}
