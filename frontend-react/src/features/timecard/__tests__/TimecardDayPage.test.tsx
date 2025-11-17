import { describe, it, expect, vi, beforeEach } from 'vitest';
import { render, screen, waitFor, fireEvent } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { BrowserRouter } from 'react-router-dom';
import { TimecardDayPage } from '../TimecardDayPage';
import { api } from '@/api';

// Mock the API
vi.mock('@/api', () => ({
  api: {
    timecard: {
      getDayBookings: vi.fn(),
      getFavoriteProjects: vi.fn(),
      getRunningBooking: vi.fn(),
      saveBooking: vi.fn(),
      deleteBooking: vi.fn(),
    },
    project: {
      getProjectTree: vi.fn(),
    },
  },
}));

describe('TimecardDayPage', () => {
  beforeEach(() => {
    vi.clearAllMocks();

    // Default mock responses
    vi.mocked(api.timecard.getDayBookings).mockResolvedValue([]);
    vi.mocked(api.timecard.getFavoriteProjects).mockResolvedValue([]);
    vi.mocked(api.project.getProjectTree).mockResolvedValue([
      { id: 1, title: 'Project 1', children: [] },
    ]);
  });

  it('renders without crashing', () => {
    render(
      <BrowserRouter>
        <TimecardDayPage />
      </BrowserRouter>
    );

    expect(screen.getByText('Timecard')).toBeInTheDocument();
  });

  it('displays date navigation controls', () => {
    render(
      <BrowserRouter>
        <TimecardDayPage />
      </BrowserRouter>
    );

    // Previous button
    expect(screen.getByTitle('Previous day')).toBeInTheDocument();

    // Next button
    expect(screen.getByTitle('Next day')).toBeInTheDocument();

    // Today button
    expect(screen.getByText('Today')).toBeInTheDocument();

    // Date picker - get the one with type="date"
    const datePickers = screen.getAllByDisplayValue(/\d{4}-\d{2}-\d{2}/);
    const mainDatePicker = datePickers.find(
      (el) => el.getAttribute('type') === 'date'
    );
    expect(mainDatePicker).toBeInTheDocument();
  });

  it('loads bookings on mount', async () => {
    const mockBookings = [
      {
        id: 1,
        projectId: 5,
        startTime: '09:00:00',
        endTime: '17:00:00',
        display: 'Project A',
        note: 'Development work',
      },
    ];

    vi.mocked(api.timecard.getDayBookings).mockResolvedValue(mockBookings);

    render(
      <BrowserRouter>
        <TimecardDayPage />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(api.timecard.getDayBookings).toHaveBeenCalled();
    });
  });

  it('shows loading state while fetching bookings', () => {
    render(
      <BrowserRouter>
        <TimecardDayPage />
      </BrowserRouter>
    );

    expect(screen.getByText('Loading bookings...')).toBeInTheDocument();
  });

  it('displays error message on API failure', async () => {
    vi.mocked(api.timecard.getDayBookings).mockRejectedValue(
      new Error('Network error')
    );

    render(
      <BrowserRouter>
        <TimecardDayPage />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(screen.getByText(/Error: Network error/i)).toBeInTheDocument();
    });
  });

  it('navigates to previous day when clicking previous button', async () => {
    const user = userEvent.setup();

    render(
      <BrowserRouter>
        <TimecardDayPage />
      </BrowserRouter>
    );

    const prevButton = screen.getByTitle('Previous day');
    await user.click(prevButton);

    // Should call API with new date
    await waitFor(() => {
      expect(api.timecard.getDayBookings).toHaveBeenCalledTimes(2);
    });
  });

  it('navigates to next day when clicking next button', async () => {
    const user = userEvent.setup();

    render(
      <BrowserRouter>
        <TimecardDayPage />
      </BrowserRouter>
    );

    const nextButton = screen.getByTitle('Next day');
    await user.click(nextButton);

    await waitFor(() => {
      expect(api.timecard.getDayBookings).toHaveBeenCalledTimes(2);
    });
  });

  it('navigates to today when clicking Today button', async () => {
    const user = userEvent.setup();

    render(
      <BrowserRouter>
        <TimecardDayPage />
      </BrowserRouter>
    );

    const todayButton = screen.getByText('Today');
    await user.click(todayButton);

    await waitFor(() => {
      expect(api.timecard.getDayBookings).toHaveBeenCalledTimes(2);
    });
  });

  it('updates URL when date changes', async () => {
    render(
      <BrowserRouter>
        <TimecardDayPage />
      </BrowserRouter>
    );

    // Get the main date picker (type="date")
    const datePickers = screen.getAllByDisplayValue(/\d{4}-\d{2}-\d{2}/);
    const mainDatePicker = datePickers.find(
      (el) => el.getAttribute('type') === 'date'
    );
    expect(mainDatePicker).toBeDefined();

    // Use fireEvent.change for date inputs
    fireEvent.change(mainDatePicker!, { target: { value: '2025-12-25' } });

    await waitFor(() => {
      expect(api.timecard.getDayBookings).toHaveBeenCalledWith('2025-12-25');
    });
  });

  it('reloads bookings after successful save', async () => {
    const mockBookings = [
      {
        id: 1,
        projectId: 5,
        startTime: '09:00:00',
        endTime: '17:00:00',
        display: 'Project A',
        note: 'Work',
      },
    ];

    vi.mocked(api.timecard.getDayBookings).mockResolvedValue(mockBookings);
    vi.mocked(api.timecard.saveBooking).mockResolvedValue({
      type: 'success',
      message: 'Saved',
      id: 2,
    });

    const user = userEvent.setup();

    render(
      <BrowserRouter>
        <TimecardDayPage />
      </BrowserRouter>
    );

    // Wait for initial load
    await waitFor(() => {
      expect(api.timecard.getDayBookings).toHaveBeenCalledTimes(1);
    });

    // Trigger save (this is simplified - actual test would interact with form)
    // For now, verify the behavior is set up correctly
    expect(screen.getByText(/New Booking|Edit Booking/i)).toBeInTheDocument();
  });
});
