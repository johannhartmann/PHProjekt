import { describe, it, expect } from 'vitest';
import { render, screen, fireEvent } from '@testing-library/react';
import { MemoryRouter, Route, Routes } from 'react-router-dom';
import { ShellLayout } from '../ShellLayout';
import { Projects } from '../../pages/Projects';
import { Calendar } from '../../pages/Calendar';
import { Timecard } from '../../pages/Timecard';
import { Home } from '../../pages/Home';

describe('Navigation', () => {
  it('navigates to Projects when clicking Projects link', () => {
    render(
      <MemoryRouter initialEntries={['/']}>
        <Routes>
          <Route element={<ShellLayout />}>
            <Route index element={<Home />} />
            <Route path="projects" element={<Projects />} />
          </Route>
        </Routes>
      </MemoryRouter>
    );

    // Click the Projects link
    const projectsLink = screen.getByRole('link', { name: /projects/i });
    fireEvent.click(projectsLink);

    // Check that we navigated to Projects page
    expect(screen.getByText('Projects', { selector: 'h1' })).toBeInTheDocument();
    expect(screen.getByText(/Manage your projects with hierarchical structure/i)).toBeInTheDocument();
  });

  it('navigates to Calendar when clicking Calendar link', () => {
    render(
      <MemoryRouter initialEntries={['/']}>
        <Routes>
          <Route element={<ShellLayout />}>
            <Route index element={<Home />} />
            <Route path="calendar" element={<Calendar />} />
          </Route>
        </Routes>
      </MemoryRouter>
    );

    // Click the Calendar link
    const calendarLink = screen.getByRole('link', { name: /calendar/i });
    fireEvent.click(calendarLink);

    // Check that we navigated to Calendar page
    expect(screen.getByText('Calendar', { selector: 'h1' })).toBeInTheDocument();
    expect(screen.getByText(/Comprehensive calendar system/i)).toBeInTheDocument();
  });

  it('navigates to Timecard when clicking Timecard link', () => {
    render(
      <MemoryRouter initialEntries={['/']}>
        <Routes>
          <Route element={<ShellLayout />}>
            <Route index element={<Home />} />
            <Route path="timecard" element={<Timecard />} />
          </Route>
        </Routes>
      </MemoryRouter>
    );

    // Click the Timecard link
    const timecardLink = screen.getByRole('link', { name: /timecard/i });
    fireEvent.click(timecardLink);

    // Check that we navigated to Timecard page
    expect(screen.getByText('Timecard', { selector: 'h1' })).toBeInTheDocument();
    expect(screen.getByText(/Track time spent on projects/i)).toBeInTheDocument();
  });

  it('highlights the active navigation link', () => {
    render(
      <MemoryRouter initialEntries={['/projects']}>
        <Routes>
          <Route element={<ShellLayout />}>
            <Route path="projects" element={<Projects />} />
          </Route>
        </Routes>
      </MemoryRouter>
    );

    // The Projects link in the sidebar should have the active class
    const projectsLinks = screen.getAllByRole('link', { name: /projects/i });
    // First link should be in the sidebar navigation
    const sidebarProjectsLink = projectsLinks.find(link =>
      link.getAttribute('href') === '/projects' && link.classList.contains('side-nav-item')
    );
    expect(sidebarProjectsLink).toHaveClass('active');
  });

  it('expands administration submenu when clicked', () => {
    render(
      <MemoryRouter initialEntries={['/']}>
        <Routes>
          <Route element={<ShellLayout />}>
            <Route index element={<Home />} />
          </Route>
        </Routes>
      </MemoryRouter>
    );

    // Administration submenu should not show Users link initially
    const usersLinksBefore = screen.queryAllByRole('link', { name: /users/i });
    // Filter for the admin/users link specifically
    const adminUsersLinkBefore = usersLinksBefore.find(link =>
      link.getAttribute('href') === '/admin/users'
    );
    expect(adminUsersLinkBefore).toBeUndefined();

    // Click the Administration button to expand
    const adminButton = screen.getByRole('button', { name: /administration/i });
    fireEvent.click(adminButton);

    // Now Users link should be visible in the admin submenu
    const usersLinksAfter = screen.getAllByRole('link', { name: /users/i });
    const adminUsersLinkAfter = usersLinksAfter.find(link =>
      link.getAttribute('href') === '/admin/users'
    );
    expect(adminUsersLinkAfter).toBeInTheDocument();

    // Roles link should also be visible
    expect(screen.getByRole('link', { name: /roles/i })).toBeInTheDocument();
  });

  it('collapses sidebar when toggle is clicked', () => {
    render(
      <MemoryRouter initialEntries={['/']}>
        <Routes>
          <Route element={<ShellLayout />}>
            <Route index element={<Home />} />
          </Route>
        </Routes>
      </MemoryRouter>
    );

    // Find the sidebar navigation
    const sidebar = screen.getByRole('navigation');
    expect(sidebar).not.toHaveClass('collapsed');

    // Click the collapse toggle
    const toggleButton = screen.getByLabelText(/collapse menu/i);
    fireEvent.click(toggleButton);

    // Sidebar should now be collapsed
    expect(sidebar).toHaveClass('collapsed');
  });

  it('renders all navigation links with correct href attributes', () => {
    render(
      <MemoryRouter initialEntries={['/']}>
        <Routes>
          <Route element={<ShellLayout />}>
            <Route index element={<Home />} />
          </Route>
        </Routes>
      </MemoryRouter>
    );

    // Check main module links
    expect(screen.getByRole('link', { name: /dashboard/i })).toHaveAttribute('href', '/');
    expect(screen.getByRole('link', { name: /projects/i })).toHaveAttribute('href', '/projects');
    expect(screen.getByRole('link', { name: /calendar/i })).toHaveAttribute('href', '/calendar');
    expect(screen.getByRole('link', { name: /timecard/i })).toHaveAttribute('href', '/timecard');
    expect(screen.getByRole('link', { name: /tickets/i })).toHaveAttribute('href', '/tickets');

    // Check tool links
    expect(screen.getByRole('link', { name: /search/i })).toHaveAttribute('href', '/search');
    expect(screen.getByRole('link', { name: /files/i })).toHaveAttribute('href', '/files');
    expect(screen.getByRole('link', { name: /tags/i })).toHaveAttribute('href', '/tags');
  });
});
