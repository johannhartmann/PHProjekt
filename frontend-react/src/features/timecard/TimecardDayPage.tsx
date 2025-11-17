import { useState, useEffect } from 'react';
import type { FormEvent } from 'react';
import { useSearchParams } from 'react-router-dom';
import { api } from '@/api';
import type { TimecardBooking } from '@/api';
import { TimecardBookingList } from './TimecardBookingList';
import { TimecardBookingForm } from './TimecardBookingForm';
import './TimecardDayPage.css';

export function TimecardDayPage() {
  const [searchParams, setSearchParams] = useSearchParams();
  const [selectedDate, setSelectedDate] = useState<Date>(() => {
    const dateParam = searchParams.get('date');
    return dateParam ? new Date(dateParam) : new Date();
  });
  const [selectedBookingId, setSelectedBookingId] = useState<number>(0);
  const [bookings, setBookings] = useState<TimecardBooking[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  // Load day bookings when date changes
  useEffect(() => {
    loadDayBookings(selectedDate);
  }, [selectedDate]);

  const loadDayBookings = async (date: Date) => {
    setLoading(true);
    setError(null);
    try {
      const dateStr = formatDate(date);
      const data = await api.timecard.getDayBookings(dateStr);
      setBookings(data);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Failed to load bookings');
    } finally {
      setLoading(false);
    }
  };

  const handleDateChange = (e: FormEvent<HTMLInputElement>) => {
    const dateStr = e.currentTarget.value;
    if (dateStr) {
      const date = new Date(dateStr);
      setSelectedDate(date);
      setSearchParams({ date: dateStr });
      setSelectedBookingId(0); // Reset to new form
    }
  };

  const handlePreviousDay = () => {
    const newDate = new Date(selectedDate);
    newDate.setDate(newDate.getDate() - 1);
    setSelectedDate(newDate);
    setSearchParams({ date: formatDate(newDate) });
    setSelectedBookingId(0);
  };

  const handleNextDay = () => {
    const newDate = new Date(selectedDate);
    newDate.setDate(newDate.getDate() + 1);
    setSelectedDate(newDate);
    setSearchParams({ date: formatDate(newDate) });
    setSelectedBookingId(0);
  };

  const handleToday = () => {
    const today = new Date();
    setSelectedDate(today);
    setSearchParams({ date: formatDate(today) });
    setSelectedBookingId(0);
  };

  const handleBookingSelect = (id: number) => {
    setSelectedBookingId(id);
  };

  const handleSaveSuccess = () => {
    loadDayBookings(selectedDate);
    setSelectedBookingId(0); // Reset to new form
  };

  const handleDeleteSuccess = () => {
    loadDayBookings(selectedDate);
    setSelectedBookingId(0); // Reset to new form
  };

  const handleNewBooking = () => {
    setSelectedBookingId(0);
  };

  return (
    <div className="timecard-day-page">
      <div className="timecard-header">
        <h1>Timecard</h1>

        <div className="timecard-date-navigation">
          <button
            type="button"
            onClick={handlePreviousDay}
            className="date-nav-button"
            title="Previous day"
          >
            ‹
          </button>

          <input
            type="date"
            value={formatDate(selectedDate)}
            onChange={handleDateChange}
            className="date-picker"
          />

          <button
            type="button"
            onClick={handleNextDay}
            className="date-nav-button"
            title="Next day"
          >
            ›
          </button>

          <button
            type="button"
            onClick={handleToday}
            className="today-button"
          >
            Today
          </button>
        </div>
      </div>

      {error && (
        <div className="timecard-error">
          <p>Error: {error}</p>
        </div>
      )}

      <div className="timecard-content">
        <div className="timecard-day-view">
          <h2>Bookings for {formatDisplayDate(selectedDate)}</h2>
          <TimecardBookingList
            bookings={bookings}
            selectedDate={selectedDate}
            selectedBookingId={selectedBookingId}
            onSelect={handleBookingSelect}
            loading={loading}
          />
        </div>

        <div className="timecard-form-view">
          <TimecardBookingForm
            bookingId={selectedBookingId}
            selectedDate={selectedDate}
            onSaveSuccess={handleSaveSuccess}
            onDeleteSuccess={handleDeleteSuccess}
            onCancel={handleNewBooking}
          />
        </div>
      </div>
    </div>
  );
}

// Helper functions

function formatDate(date: Date): string {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

function formatDisplayDate(date: Date): string {
  return date.toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
}
