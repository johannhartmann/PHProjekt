import { ModuleStub } from '../components/ModuleStub';

export function Tickets() {
  return (
    <ModuleStub
      icon="🎫"
      title="Tickets"
      description="Issue tracking and request management system. Create, assign, and track tickets through their lifecycle with status updates, priorities, and assignments."
      features={[
        'Ticket creation and assignment',
        'Priority and status management',
        'Custom ticket fields',
        'Attachment support',
        'Comment and activity history',
        'Email notifications',
        'Tag-based categorization',
        'Advanced filtering and search',
        'Workflow customization',
        'SLA tracking',
      ]}
      dojoPath="/index.php#Helpdesk"
    />
  );
}
