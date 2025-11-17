import { describe, it, expect, vi } from 'vitest';
import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { TimecardBookingList } from '../TimecardBookingList';
import type { TimecardBooking } from '@/api';

describe('TimecardBookingList', () => {
  const mockBookings: TimecardBooking[] = [
    {
      id: 1,
      projectId: 5,
      startTime: '09:00:00',
      endTime: '17:00:00',
      display: 'Project A',
      note: 'Development work',
    },
    {
      id: 2,
      projectId: 7,
      startTime: '18:00:00',
      endTime: null,
      display: 'Project B',
      note: 'Currently working',
    },
  ];

  it('renders without crashing', () => {
    render(
      <TimecardBookingList
        bookings={[]}
        selectedBookingId={0}
        onSelect={vi.fn()}
        loading={false}
      />
    );

    expect(screen.getByText('No bookings for this day.')).toBeInTheDocument();
  });

  it('displays loading state', () => {
    render(
      <TimecardBookingList
        bookings={[]}
        selectedBookingId={0}
        onSelect={vi.fn()}
        loading={true}
      />
    );

    expect(screen.getByText('Loading bookings...')).toBeInTheDocument();
  });

  it('displays empty state when no bookings', () => {
    render(
      <TimecardBookingList
        bookings={[]}
        selectedBookingId={0}
        onSelect={vi.fn()}
        loading={false}
      />
    );

    expect(screen.getByText('No bookings for this day.')).toBeInTheDocument();
    expect(
      screen.getByText('Click "New" to create a booking.')
    ).toBeInTheDocument();
  });

  it('displays list of bookings', () => {
    render(
      <TimecardBookingList
        bookings={mockBookings}
        selectedBookingId={0}
        onSelect={vi.fn()}
        loading={false}
      />
    );

    expect(screen.getByText('Project A')).toBeInTheDocument();
    expect(screen.getByText('Project B')).toBeInTheDocument();
    expect(screen.getByText('Development work')).toBeInTheDocument();
    expect(screen.getByText('Currently working')).toBeInTheDocument();
  });

  it('displays start and end times correctly', () => {
    render(
      <TimecardBookingList
        bookings={mockBookings}
        selectedBookingId={0}
        onSelect={vi.fn()}
        loading={false}
      />
    );

    // First booking: 09:00 - 17:00
    expect(screen.getByText('09:00')).toBeInTheDocument();
    expect(screen.getByText('17:00')).toBeInTheDocument();

    // Second booking: 18:00 - (running)
    expect(screen.getByText('18:00')).toBeInTheDocument();
  });

  it('displays running indicator for booking without end time', () => {
    render(
      <TimecardBookingList
        bookings={mockBookings}
        selectedBookingId={0}
        onSelect={vi.fn()}
        loading={false}
      />
    );

    // Running indicator should be present
    expect(screen.getByText('--:--')).toBeInTheDocument();
  });

  it('calculates and displays total hours', () => {
    render(
      <TimecardBookingList
        bookings={mockBookings}
        selectedBookingId={0}
        onSelect={vi.fn()}
        loading={false}
      />
    );

    // First booking is 8 hours (09:00 to 17:00)
    // Second booking is running (not counted)
    // Total should be 08:00
    expect(screen.getByText(/Total: 08:00/i)).toBeInTheDocument();
  });

  it('highlights selected booking', () => {
    const { container } = render(
      <TimecardBookingList
        bookings={mockBookings}
        selectedBookingId={1}
        onSelect={vi.fn()}
        loading={false}
      />
    );

    const selectedBooking = container.querySelector('.booking-entry.selected');
    expect(selectedBooking).toBeInTheDocument();
  });

  it('calls onSelect when booking is clicked', async () => {
    const mockOnSelect = vi.fn();
    const user = userEvent.setup();

    render(
      <TimecardBookingList
        bookings={mockBookings}
        selectedBookingId={0}
        onSelect={mockOnSelect}
        loading={false}
      />
    );

    const firstBooking = screen.getByText('Project A').closest('.booking-entry');
    if (firstBooking) {
      await user.click(firstBooking);
    }

    expect(mockOnSelect).toHaveBeenCalledWith(1);
  });

  it('applies running class to booking without end time', () => {
    const { container } = render(
      <TimecardBookingList
        bookings={mockBookings}
        selectedBookingId={0}
        onSelect={vi.fn()}
        loading={false}
      />
    );

    const runningBookings = container.querySelectorAll('.booking-entry.running');
    expect(runningBookings).toHaveLength(1);
  });

  it('displays duration for completed bookings', () => {
    render(
      <TimecardBookingList
        bookings={mockBookings}
        selectedBookingId={0}
        onSelect={vi.fn()}
        loading={false}
      />
    );

    // 09:00 to 17:00 = 8 hours = 08:00
    expect(screen.getByText('08:00')).toBeInTheDocument();
  });

  it('handles bookings with Unassigned project', () => {
    const unassignedBooking: TimecardBooking[] = [
      {
        id: 3,
        projectId: 1,
        startTime: '10:00:00',
        endTime: '11:00:00',
        display: 'Unassigned',
        note: 'Break',
      },
    ];

    render(
      <TimecardBookingList
        bookings={unassignedBooking}
        selectedBookingId={0}
        onSelect={vi.fn()}
        loading={false}
      />
    );

    expect(screen.getByText('Unassigned')).toBeInTheDocument();
  });

  it('handles keyboard navigation (Enter key)', async () => {
    const mockOnSelect = vi.fn();
    const user = userEvent.setup();

    render(
      <TimecardBookingList
        bookings={mockBookings}
        selectedBookingId={0}
        onSelect={mockOnSelect}
        loading={false}
      />
    );

    const firstBooking = screen.getByText('Project A').closest('.booking-entry');
    if (firstBooking) {
      firstBooking.focus();
      await user.keyboard('{Enter}');
    }

    expect(mockOnSelect).toHaveBeenCalledWith(1);
  });

  it('handles keyboard navigation (Space key)', async () => {
    const mockOnSelect = vi.fn();
    const user = userEvent.setup();

    render(
      <TimecardBookingList
        bookings={mockBookings}
        selectedBookingId={0}
        onSelect={mockOnSelect}
        loading={false}
      />
    );

    const firstBooking = screen.getByText('Project A').closest('.booking-entry');
    if (firstBooking) {
      firstBooking.focus();
      await user.keyboard(' ');
    }

    expect(mockOnSelect).toHaveBeenCalledWith(1);
  });

  it('calculates total correctly with multiple bookings', () => {
    const multipleBookings: TimecardBooking[] = [
      {
        id: 1,
        projectId: 5,
        startTime: '09:00:00',
        endTime: '12:00:00',
        display: 'Project A',
        note: 'Morning work',
      },
      {
        id: 2,
        projectId: 6,
        startTime: '13:00:00',
        endTime: '17:30:00',
        display: 'Project B',
        note: 'Afternoon work',
      },
    ];

    render(
      <TimecardBookingList
        bookings={multipleBookings}
        selectedBookingId={0}
        onSelect={vi.fn()}
        loading={false}
      />
    );

    // 3 hours + 4.5 hours = 7.5 hours = 07:30
    expect(screen.getByText(/Total: 07:30/i)).toBeInTheDocument();
  });
});
