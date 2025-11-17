import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import { MemoryRouter } from 'react-router-dom';
import { ShellLayout } from '../ShellLayout';

describe('ShellLayout', () => {
  it('renders the top navigation', () => {
    render(
      <MemoryRouter>
        <ShellLayout />
      </MemoryRouter>
    );

    // Check for PHProjekt branding
    expect(screen.getByText('PHProjekt')).toBeInTheDocument();
    expect(screen.getByText('6.0')).toBeInTheDocument();
  });

  it('renders the side navigation', () => {
    render(
      <MemoryRouter>
        <ShellLayout />
      </MemoryRouter>
    );

    // Check for main navigation items
    expect(screen.getByText('Dashboard')).toBeInTheDocument();
    expect(screen.getByText('Projects')).toBeInTheDocument();
    expect(screen.getByText('Calendar')).toBeInTheDocument();
    expect(screen.getByText('Timecard')).toBeInTheDocument();
  });

  it('renders the user menu', () => {
    render(
      <MemoryRouter>
        <ShellLayout />
      </MemoryRouter>
    );

    // Check for user menu elements
    expect(screen.getByText('Demo User')).toBeInTheDocument();
  });

  it('renders the language selector', () => {
    render(
      <MemoryRouter>
        <ShellLayout />
      </MemoryRouter>
    );

    // Check for language selector
    expect(screen.getByText('EN')).toBeInTheDocument();
  });

  it('renders all main module links', () => {
    render(
      <MemoryRouter>
        <ShellLayout />
      </MemoryRouter>
    );

    // Main modules
    expect(screen.getByText('Dashboard')).toBeInTheDocument();
    expect(screen.getByText('Projects')).toBeInTheDocument();
    expect(screen.getByText('Calendar')).toBeInTheDocument();
    expect(screen.getByText('Timecard')).toBeInTheDocument();
    expect(screen.getByText('Tickets')).toBeInTheDocument();
  });

  it('renders tool links', () => {
    render(
      <MemoryRouter>
        <ShellLayout />
      </MemoryRouter>
    );

    // Tools
    expect(screen.getByText('Search')).toBeInTheDocument();
    expect(screen.getByText('Files')).toBeInTheDocument();
    expect(screen.getByText('Tags')).toBeInTheDocument();
  });

  it('renders administration section', () => {
    render(
      <MemoryRouter>
        <ShellLayout />
      </MemoryRouter>
    );

    // Administration expandable button
    expect(screen.getByText('Administration')).toBeInTheDocument();
  });

  it('has a collapse toggle button', () => {
    render(
      <MemoryRouter>
        <ShellLayout />
      </MemoryRouter>
    );

    // Sidebar collapse toggle
    const toggleButton = screen.getByLabelText(/collapse menu/i);
    expect(toggleButton).toBeInTheDocument();
  });
});
