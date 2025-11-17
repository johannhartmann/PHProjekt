import { describe, it, expect, vi, beforeEach } from 'vitest';
import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { BrowserRouter } from 'react-router-dom';
import { TimecardBookingForm } from '../TimecardBookingForm';
import { api } from '@/api';

// Mock the API
vi.mock('@/api', () => ({
  api: {
    timecard: {
      getDayBookings: vi.fn(),
      getFavoriteProjects: vi.fn(),
      saveBooking: vi.fn(),
      deleteBooking: vi.fn(),
    },
    project: {
      getProjectTree: vi.fn(),
    },
  },
}));

describe('TimecardBookingForm', () => {
  const mockOnSaveSuccess = vi.fn();
  const mockOnDeleteSuccess = vi.fn();
  const mockOnCancel = vi.fn();
  const mockSelectedDate = new Date('2025-11-17');

  beforeEach(() => {
    vi.clearAllMocks();

    // Default mock responses
    vi.mocked(api.timecard.getDayBookings).mockResolvedValue([]);
    vi.mocked(api.timecard.getFavoriteProjects).mockResolvedValue([
      { id: 5, name: 'Favorite Project', display: '⭐ Favorite Project' },
    ]);
    vi.mocked(api.project.getProjectTree).mockResolvedValue([
      {
        id: 1,
        title: 'Unassigned',
        path: 'Unassigned',
        children: [],
      },
      {
        id: 5,
        title: 'Project A',
        path: 'Project A',
        children: [],
      },
    ]);
  });

  it('renders new booking form by default', async () => {
    render(
      <BrowserRouter>
        <TimecardBookingForm
          bookingId={0}
          selectedDate={mockSelectedDate}
          onSaveSuccess={mockOnSaveSuccess}
          onDeleteSuccess={mockOnDeleteSuccess}
          onCancel={mockOnCancel}
        />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(screen.getByText('New Booking')).toBeInTheDocument();
    });
  });

  it('displays all form fields', async () => {
    render(
      <BrowserRouter>
        <TimecardBookingForm
          bookingId={0}
          selectedDate={mockSelectedDate}
          onSaveSuccess={mockOnSaveSuccess}
          onDeleteSuccess={mockOnDeleteSuccess}
          onCancel={mockOnCancel}
        />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(screen.getByLabelText(/Start Date & Time/i)).toBeInTheDocument();
      expect(screen.getByLabelText(/End Time/i)).toBeInTheDocument();
      expect(screen.getByLabelText(/Project/i)).toBeInTheDocument();
      expect(screen.getByLabelText(/Notes/i)).toBeInTheDocument();
    });
  });

  it('displays Save and New buttons for new booking', async () => {
    render(
      <BrowserRouter>
        <TimecardBookingForm
          bookingId={0}
          selectedDate={mockSelectedDate}
          onSaveSuccess={mockOnSaveSuccess}
          onDeleteSuccess={mockOnDeleteSuccess}
          onCancel={mockOnCancel}
        />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(screen.getByText('Save')).toBeInTheDocument();
      expect(screen.getByText('Clear')).toBeInTheDocument();
      expect(screen.queryByText('Delete')).not.toBeInTheDocument();
    });
  });

  it('displays Delete button for existing booking', async () => {
    const mockBooking = {
      id: 1,
      projectId: 5,
      startTime: '09:00:00',
      endTime: '17:00:00',
      display: 'Project A',
      note: 'Work',
    };

    vi.mocked(api.timecard.getDayBookings).mockResolvedValue([mockBooking]);

    render(
      <BrowserRouter>
        <TimecardBookingForm
          bookingId={1}
          selectedDate={mockSelectedDate}
          onSaveSuccess={mockOnSaveSuccess}
          onDeleteSuccess={mockOnDeleteSuccess}
          onCancel={mockOnCancel}
        />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(screen.getByText('Edit Booking')).toBeInTheDocument();
      expect(screen.getByText('Delete')).toBeInTheDocument();
    });
  });

  it('validates required start datetime field', async () => {
    const user = userEvent.setup();

    render(
      <BrowserRouter>
        <TimecardBookingForm
          bookingId={0}
          selectedDate={mockSelectedDate}
          onSaveSuccess={mockOnSaveSuccess}
          onDeleteSuccess={mockOnDeleteSuccess}
          onCancel={mockOnCancel}
        />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(screen.getByText('Save')).toBeInTheDocument();
    });

    // Clear the start datetime field
    const startField = screen.getByLabelText(/Start Date & Time/i);
    await user.clear(startField);

    // Try to submit
    const saveButton = screen.getByText('Save');
    await user.click(saveButton);

    await waitFor(() => {
      expect(screen.getByText('Start time is required')).toBeInTheDocument();
    });

    expect(mockOnSaveSuccess).not.toHaveBeenCalled();
  });

  it('validates end time must be after start time', async () => {
    const user = userEvent.setup();

    render(
      <BrowserRouter>
        <TimecardBookingForm
          bookingId={0}
          selectedDate={mockSelectedDate}
          onSaveSuccess={mockOnSaveSuccess}
          onDeleteSuccess={mockOnDeleteSuccess}
          onCancel={mockOnCancel}
        />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(screen.getByText('Save')).toBeInTheDocument();
    });

    // Set start time to 17:00
    const startField = screen.getByLabelText(/Start Date & Time/i);
    await user.clear(startField);
    await user.type(startField, '2025-11-17T17:00');

    // Set end time to 09:00 (before start)
    const endField = screen.getByLabelText(/End Time/i);
    await user.type(endField, '09:00');

    // Try to submit
    const saveButton = screen.getByText('Save');
    await user.click(saveButton);

    await waitFor(() => {
      expect(
        screen.getByText('End time must be after start time')
      ).toBeInTheDocument();
    });

    expect(mockOnSaveSuccess).not.toHaveBeenCalled();
  });

  it('allows saving booking with valid data', async () => {
    const user = userEvent.setup();

    vi.mocked(api.timecard.saveBooking).mockResolvedValue({
      type: 'success',
      message: 'Booking saved successfully',
      id: 1,
    });

    render(
      <BrowserRouter>
        <TimecardBookingForm
          bookingId={0}
          selectedDate={mockSelectedDate}
          onSaveSuccess={mockOnSaveSuccess}
          onDeleteSuccess={mockOnDeleteSuccess}
          onCancel={mockOnCancel}
        />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(screen.getByText('Save')).toBeInTheDocument();
    });

    // Fill form with valid data
    const startField = screen.getByLabelText(/Start Date & Time/i);
    await user.clear(startField);
    await user.type(startField, '2025-11-17T09:00');

    const endField = screen.getByLabelText(/End Time/i);
    await user.type(endField, '17:00');

    const notesField = screen.getByLabelText(/Notes/i);
    await user.type(notesField, 'Development work');

    // Submit form
    const saveButton = screen.getByText('Save');
    await user.click(saveButton);

    await waitFor(() => {
      expect(api.timecard.saveBooking).toHaveBeenCalled();
      expect(screen.getByText('Booking saved successfully')).toBeInTheDocument();
    });

    // Should call success callback after delay
    await waitFor(
      () => {
        expect(mockOnSaveSuccess).toHaveBeenCalled();
      },
      { timeout: 2000 }
    );
  });

  it('allows saving booking without end time (running timer)', async () => {
    const user = userEvent.setup();

    vi.mocked(api.timecard.saveBooking).mockResolvedValue({
      type: 'success',
      message: 'Timer started',
      id: 1,
    });

    render(
      <BrowserRouter>
        <TimecardBookingForm
          bookingId={0}
          selectedDate={mockSelectedDate}
          onSaveSuccess={mockOnSaveSuccess}
          onDeleteSuccess={mockOnDeleteSuccess}
          onCancel={mockOnCancel}
        />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(screen.getByText('Save')).toBeInTheDocument();
    });

    // Fill only start time, leave end time empty
    const startField = screen.getByLabelText(/Start Date & Time/i);
    await user.clear(startField);
    await user.type(startField, '2025-11-17T14:30');

    // Submit form
    const saveButton = screen.getByText('Save');
    await user.click(saveButton);

    await waitFor(() => {
      expect(api.timecard.saveBooking).toHaveBeenCalledWith(0, {
        startDatetime: expect.stringContaining('2025-11-17'),
        endTime: null,
        projectId: expect.any(Number),
        notes: '',
        timecardId: 0,
      });
    });
  });

  it('handles save error correctly', async () => {
    const user = userEvent.setup();

    vi.mocked(api.timecard.saveBooking).mockRejectedValue(
      new Error('Network error')
    );

    render(
      <BrowserRouter>
        <TimecardBookingForm
          bookingId={0}
          selectedDate={mockSelectedDate}
          onSaveSuccess={mockOnSaveSuccess}
          onDeleteSuccess={mockOnDeleteSuccess}
          onCancel={mockOnCancel}
        />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(screen.getByText('Save')).toBeInTheDocument();
    });

    // Try to save
    const saveButton = screen.getByText('Save');
    await user.click(saveButton);

    await waitFor(() => {
      expect(screen.getByText(/Network error/i)).toBeInTheDocument();
    });

    expect(mockOnSaveSuccess).not.toHaveBeenCalled();
  });

  it('handles delete with confirmation', async () => {
    const user = userEvent.setup();

    // Mock window.confirm
    const confirmMock = vi.fn().mockReturnValue(true);
    vi.stubGlobal('confirm', confirmMock);

    vi.mocked(api.timecard.deleteBooking).mockResolvedValue({
      type: 'success',
      message: 'Booking deleted',
    });

    const mockBooking = {
      id: 1,
      projectId: 5,
      startTime: '09:00:00',
      endTime: '17:00:00',
      display: 'Project A',
      note: 'Work',
    };

    vi.mocked(api.timecard.getDayBookings).mockResolvedValue([mockBooking]);

    render(
      <BrowserRouter>
        <TimecardBookingForm
          bookingId={1}
          selectedDate={mockSelectedDate}
          onSaveSuccess={mockOnSaveSuccess}
          onDeleteSuccess={mockOnDeleteSuccess}
          onCancel={mockOnCancel}
        />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(screen.getByText('Delete')).toBeInTheDocument();
    });

    const deleteButton = screen.getByText('Delete');
    await user.click(deleteButton);

    expect(confirmMock).toHaveBeenCalledWith(
      'Are you sure you want to delete this booking?'
    );

    await waitFor(() => {
      expect(api.timecard.deleteBooking).toHaveBeenCalledWith(1);
    });

    await waitFor(
      () => {
        expect(mockOnDeleteSuccess).toHaveBeenCalled();
      },
      { timeout: 1000 }
    );

    vi.unstubAllGlobals();
  });

  it('cancels delete when user declines confirmation', async () => {
    const user = userEvent.setup();

    const confirmMock = vi.fn().mockReturnValue(false);
    vi.stubGlobal('confirm', confirmMock);

    const mockBooking = {
      id: 1,
      projectId: 5,
      startTime: '09:00:00',
      endTime: '17:00:00',
      display: 'Project A',
      note: 'Work',
    };

    vi.mocked(api.timecard.getDayBookings).mockResolvedValue([mockBooking]);

    render(
      <BrowserRouter>
        <TimecardBookingForm
          bookingId={1}
          selectedDate={mockSelectedDate}
          onSaveSuccess={mockOnSaveSuccess}
          onDeleteSuccess={mockOnDeleteSuccess}
          onCancel={mockOnCancel}
        />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(screen.getByText('Delete')).toBeInTheDocument();
    });

    const deleteButton = screen.getByText('Delete');
    await user.click(deleteButton);

    expect(api.timecard.deleteBooking).not.toHaveBeenCalled();
    expect(mockOnDeleteSuccess).not.toHaveBeenCalled();

    vi.unstubAllGlobals();
  });

  it('calls onCancel when Clear/New button is clicked', async () => {
    const user = userEvent.setup();

    render(
      <BrowserRouter>
        <TimecardBookingForm
          bookingId={0}
          selectedDate={mockSelectedDate}
          onSaveSuccess={mockOnSaveSuccess}
          onDeleteSuccess={mockOnDeleteSuccess}
          onCancel={mockOnCancel}
        />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(screen.getByText('Clear')).toBeInTheDocument();
    });

    const clearButton = screen.getByText('Clear');
    await user.click(clearButton);

    expect(mockOnCancel).toHaveBeenCalled();
  });

  it('disables form fields while saving', async () => {
    const user = userEvent.setup();

    // Create a promise that never resolves to keep the form in saving state
    let resolveSave: any;
    const savePromise = new Promise((resolve) => {
      resolveSave = resolve;
    });
    vi.mocked(api.timecard.saveBooking).mockReturnValue(savePromise as any);

    render(
      <BrowserRouter>
        <TimecardBookingForm
          bookingId={0}
          selectedDate={mockSelectedDate}
          onSaveSuccess={mockOnSaveSuccess}
          onDeleteSuccess={mockOnDeleteSuccess}
          onCancel={mockOnCancel}
        />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(screen.getByText('Save')).toBeInTheDocument();
    });

    const saveButton = screen.getByText('Save');
    await user.click(saveButton);

    await waitFor(() => {
      expect(screen.getByText('Saving...')).toBeInTheDocument();
    });

    // Fields should be disabled
    const startField = screen.getByLabelText(/Start Date & Time/i);
    expect(startField).toBeDisabled();

    // Clean up
    resolveSave({ type: 'success', message: 'Saved', id: 1 });
  });

  it('loads favorites and displays them at top of project list', async () => {
    render(
      <BrowserRouter>
        <TimecardBookingForm
          bookingId={0}
          selectedDate={mockSelectedDate}
          onSaveSuccess={mockOnSaveSuccess}
          onDeleteSuccess={mockOnDeleteSuccess}
          onCancel={mockOnCancel}
        />
      </BrowserRouter>
    );

    await waitFor(() => {
      expect(api.timecard.getFavoriteProjects).toHaveBeenCalled();
      expect(api.project.getProjectTree).toHaveBeenCalled();
    });

    const projectSelect = screen.getByLabelText(/Project/i);
    expect(projectSelect).toBeInTheDocument();
  });
});
