import { ModuleStub } from '../components/ModuleStub';

export function Timecard() {
  return (
    <ModuleStub
      icon="⏱️"
      title="Timecard"
      description="Track time spent on projects with precision. Record your work hours, manage multiple timers, and generate comprehensive time reports for billing and productivity analysis."
      features={[
        'Time tracking with start/end times',
        'Daily booking list with automatic totals',
        'Monthly summary grid view',
        'Favorite projects for quick access',
        'Running bookings (active timers)',
        'Project-based time allocation',
        'CSV export for payroll and reporting',
        'Drag & drop time entry',
        'Personal statistics and analytics',
        'Team statistics overview',
      ]}
      dojoPath="/index.php#Timecard"
    />
  );
}
