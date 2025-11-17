import type { TimecardBooking } from '@/api';
import './TimecardBookingList.css';

interface TimecardBookingListProps {
  bookings: TimecardBooking[];
  selectedDate: Date;
  selectedBookingId: number;
  onSelect: (id: number) => void;
  loading: boolean;
}

export function TimecardBookingList({
  bookings,
  selectedBookingId,
  onSelect,
  loading,
}: TimecardBookingListProps) {
  if (loading) {
    return (
      <div className="booking-list-loading">
        <div className="spinner"></div>
        <p>Loading bookings...</p>
      </div>
    );
  }

  const totalMinutes = calculateTotalMinutes(bookings);

  return (
    <div className="booking-list">
      {bookings.length === 0 ? (
        <div className="booking-list-empty">
          <p>No bookings for this day.</p>
          <p className="empty-hint">Click "New" to create a booking.</p>
        </div>
      ) : (
        <>
          <div className="booking-list-entries">
            {bookings.map((booking) => (
              <BookingEntry
                key={booking.id}
                booking={booking}
                isSelected={booking.id === selectedBookingId}
                onClick={() => onSelect(booking.id)}
              />
            ))}
          </div>

          <div className="booking-list-footer">
            <strong>Total: {formatMinutes(totalMinutes)}</strong>
          </div>
        </>
      )}
    </div>
  );
}

interface BookingEntryProps {
  booking: TimecardBooking;
  isSelected: boolean;
  onClick: () => void;
}

function BookingEntry({ booking, isSelected, onClick }: BookingEntryProps) {
  const isRunning = booking.endTime === null || booking.endTime === '';
  const duration = isRunning
    ? null
    : calculateDuration(booking.startTime, booking.endTime!);

  const projectName =
    booking.projectId === 1 ? 'Unassigned' : booking.display || 'Unknown Project';

  return (
    <div
      className={`booking-entry ${isSelected ? 'selected' : ''} ${isRunning ? 'running' : ''}`}
      onClick={onClick}
      role="button"
      tabIndex={0}
      onKeyDown={(e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          onClick();
        }
      }}
    >
      <div className="booking-time">
        <span className="booking-start">{formatTime(booking.startTime)}</span>
        <span className="booking-separator">-</span>
        <span className="booking-end">
          {isRunning ? (
            <span className="running-indicator">
              --:-- <span className="running-dot">●</span>
            </span>
          ) : (
            formatTime(booking.endTime!)
          )}
        </span>
      </div>

      <div className="booking-duration">
        {duration !== null ? formatMinutes(duration) : ''}
      </div>

      <div className="booking-project">{projectName}</div>

      {booking.note && (
        <div className="booking-notes">{booking.note}</div>
      )}
    </div>
  );
}

// Helper functions

function calculateTotalMinutes(bookings: TimecardBooking[]): number {
  return bookings.reduce((total, booking) => {
    if (booking.endTime === null || booking.endTime === '') {
      return total; // Skip running bookings
    }
    const minutes = calculateDuration(booking.startTime, booking.endTime);
    return total + minutes;
  }, 0);
}

function calculateDuration(startTime: string, endTime: string): number {
  const start = parseTime(startTime);
  const end = parseTime(endTime);
  return Math.floor((end.getTime() - start.getTime()) / 1000 / 60);
}

function parseTime(timeStr: string): Date {
  // Parse HH:MM:SS or HH:MM format
  const parts = timeStr.split(':');
  const hours = parseInt(parts[0], 10);
  const minutes = parseInt(parts[1], 10) || 0;
  const seconds = parseInt(parts[2], 10) || 0;

  const date = new Date(0);
  date.setHours(hours, minutes, seconds);
  return date;
}

function formatTime(timeStr: string): string {
  // Convert HH:MM:SS to HH:MM
  const parts = timeStr.split(':');
  return `${parts[0]}:${parts[1]}`;
}

function formatMinutes(minutes: number): string {
  const hours = Math.floor(minutes / 60);
  const mins = minutes % 60;
  return `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}`;
}
