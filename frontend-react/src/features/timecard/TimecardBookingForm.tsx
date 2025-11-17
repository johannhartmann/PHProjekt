import { useState, useEffect } from 'react';
import type { FormEvent } from 'react';
import { api } from '@/api';
import './TimecardBookingForm.css';

interface TimecardBookingFormProps {
  bookingId: number;
  selectedDate: Date;
  onSaveSuccess: () => void;
  onDeleteSuccess: () => void;
  onCancel: () => void;
}

interface FormData {
  startDatetime: string;
  endTime: string;
  projectId: number;
  notes: string;
}

interface ProjectOption {
  id: number;
  name: string;
  isFavorite?: boolean;
}

export function TimecardBookingForm({
  bookingId,
  selectedDate,
  onSaveSuccess,
  onDeleteSuccess,
  onCancel,
}: TimecardBookingFormProps) {
  const [formData, setFormData] = useState<FormData>({
    startDatetime: getCurrentDateTime(selectedDate),
    endTime: '',
    projectId: 1,
    notes: '',
  });
  const [projects, setProjects] = useState<ProjectOption[]>([]);
  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [message, setMessage] = useState<{
    type: 'success' | 'error' | 'warning';
    text: string;
  } | null>(null);

  // Load booking data and projects when bookingId or selectedDate changes
  useEffect(() => {
    if (bookingId === 0) {
      // New booking - reset form with current date/time
      setFormData({
        startDatetime: getCurrentDateTime(selectedDate),
        endTime: '',
        projectId: 1,
        notes: '',
      });
      setErrors({});
      setMessage(null);
    } else {
      loadBooking(bookingId);
    }
  }, [bookingId, selectedDate]);

  // Load favorites and merge with projects
  useEffect(() => {
    loadProjects();
  }, []);

  const loadBooking = async (id: number) => {
    setLoading(true);
    try {
      const bookings = await api.timecard.getDayBookings(formatDate(selectedDate));
      const booking = bookings.find((b) => b.id === id);

      if (booking) {
        setFormData({
          startDatetime: `${formatDate(selectedDate)} ${booking.startTime}`,
          endTime: booking.endTime || '',
          projectId: booking.projectId,
          notes: booking.note || '',
        });
        setErrors({});
        setMessage(null);
      }
    } catch (err) {
      setMessage({
        type: 'error',
        text: err instanceof Error ? err.message : 'Failed to load booking',
      });
    } finally {
      setLoading(false);
    }
  };

  const loadProjects = async () => {
    try {
      // Load favorites
      const favorites = await api.timecard.getFavoriteProjects();

      // Load all projects from project tree
      const projectTree = await api.project.getProjectTree();

      // Convert tree to flat list
      const allProjects = flattenProjectTree(projectTree);

      // Merge favorites with projects (favorites first)
      const favoriteIds = new Set(favorites.map((f) => f.id));
      const favProjects: ProjectOption[] = favorites.map((f) => ({
        id: f.id,
        name: f.display || f.name,
        isFavorite: true,
      }));

      const otherProjects: ProjectOption[] = allProjects
        .filter((p) => !favoriteIds.has(p.id))
        .map((p) => ({
          id: p.id,
          name: p.path || p.title,
          isFavorite: false,
        }));

      // Add "Unassigned" option
      const unassigned: ProjectOption = {
        id: 1,
        name: 'Unassigned',
        isFavorite: false,
      };

      setProjects([unassigned, ...favProjects, ...otherProjects]);
    } catch (err) {
      console.error('Failed to load projects:', err);
      // Fallback to just "Unassigned"
      setProjects([{ id: 1, name: 'Unassigned', isFavorite: false }]);
    }
  };

  const handleChange = (
    e: FormEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>
  ) => {
    const { name, value } = e.currentTarget;
    setFormData((prev) => ({ ...prev, [name]: value }));

    // Clear error for this field
    if (errors[name]) {
      setErrors((prev) => {
        const newErrors = { ...prev };
        delete newErrors[name];
        return newErrors;
      });
    }
  };

  const validateForm = (): boolean => {
    const newErrors: Record<string, string> = {};

    // Validate start datetime
    if (!formData.startDatetime) {
      newErrors.startDatetime = 'Start time is required';
    }

    // Validate project
    if (!formData.projectId || formData.projectId < 0) {
      newErrors.projectId = 'Please select a project';
    }

    // Validate end time if provided
    if (formData.endTime) {
      // Extract time parts
      const startTime = formData.startDatetime.substring(11); // HH:MM:SS
      const endTime = formData.endTime;

      if (endTime !== '00:00' && endTime !== '00:00:00') {
        const startMinutes = timeToMinutes(startTime);
        const endMinutes = timeToMinutes(endTime);

        if (endMinutes <= startMinutes) {
          newErrors.endTime = 'End time must be after start time';
        }
      }
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    if (!validateForm()) {
      setMessage({
        type: 'error',
        text: 'Please fix the errors below',
      });
      return;
    }

    setSaving(true);
    setMessage(null);

    try {
      const response = await api.timecard.saveBooking(bookingId, {
        startDatetime: formData.startDatetime,
        endTime: formData.endTime || null,
        projectId: formData.projectId,
        notes: formData.notes,
        timecardId: bookingId,
      });

      if (response.type === 'success') {
        setMessage({
          type: 'success',
          text: response.message || 'Booking saved successfully',
        });
        setTimeout(() => {
          onSaveSuccess();
        }, 1000);
      } else {
        setMessage({
          type: 'error',
          text: response.message || 'Failed to save booking',
        });
      }
    } catch (err) {
      setMessage({
        type: 'error',
        text: err instanceof Error ? err.message : 'Failed to save booking',
      });
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async () => {
    if (!confirm('Are you sure you want to delete this booking?')) {
      return;
    }

    setSaving(true);
    setMessage(null);

    try {
      const response = await api.timecard.deleteBooking(bookingId);

      if (response.type === 'success') {
        setMessage({
          type: 'success',
          text: response.message || 'Booking deleted successfully',
        });
        setTimeout(() => {
          onDeleteSuccess();
        }, 500);
      } else {
        setMessage({
          type: 'error',
          text: response.message || 'Failed to delete booking',
        });
      }
    } catch (err) {
      setMessage({
        type: 'error',
        text: err instanceof Error ? err.message : 'Failed to delete booking',
      });
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return (
      <div className="booking-form-loading">
        <div className="spinner"></div>
        <p>Loading...</p>
      </div>
    );
  }

  return (
    <div className="booking-form">
      <h3>{bookingId === 0 ? 'New Booking' : 'Edit Booking'}</h3>

      {message && (
        <div className={`booking-form-message ${message.type}`}>
          {message.text}
        </div>
      )}

      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <label htmlFor="startDatetime">
            Start Date & Time <span className="required">*</span>
          </label>
          <input
            type="datetime-local"
            id="startDatetime"
            name="startDatetime"
            value={formData.startDatetime}
            onChange={handleChange}
            disabled={saving}
            className={errors.startDatetime ? 'error' : ''}
          />
          {errors.startDatetime && (
            <span className="error-message">{errors.startDatetime}</span>
          )}
        </div>

        <div className="form-group">
          <label htmlFor="endTime">
            End Time
            <span className="hint">(leave empty for running timer)</span>
          </label>
          <input
            type="time"
            id="endTime"
            name="endTime"
            value={formData.endTime}
            onChange={handleChange}
            disabled={saving}
            className={errors.endTime ? 'error' : ''}
          />
          {errors.endTime && (
            <span className="error-message">{errors.endTime}</span>
          )}
        </div>

        <div className="form-group">
          <label htmlFor="projectId">
            Project <span className="required">*</span>
          </label>
          <select
            id="projectId"
            name="projectId"
            value={formData.projectId}
            onChange={handleChange}
            disabled={saving}
            className={errors.projectId ? 'error' : ''}
          >
            {projects.map((project) => (
              <option key={project.id} value={project.id}>
                {project.isFavorite ? '⭐ ' : ''}
                {project.name}
              </option>
            ))}
          </select>
          {errors.projectId && (
            <span className="error-message">{errors.projectId}</span>
          )}
        </div>

        <div className="form-group">
          <label htmlFor="notes">Notes</label>
          <textarea
            id="notes"
            name="notes"
            value={formData.notes}
            onChange={handleChange}
            disabled={saving}
            rows={4}
            placeholder="Enter notes or description..."
          />
        </div>

        <div className="form-actions">
          <button
            type="submit"
            className="button-primary"
            disabled={saving}
          >
            {saving ? 'Saving...' : 'Save'}
          </button>

          {bookingId > 0 && (
            <button
              type="button"
              onClick={handleDelete}
              className="button-danger"
              disabled={saving}
            >
              Delete
            </button>
          )}

          <button
            type="button"
            onClick={onCancel}
            className="button-secondary"
            disabled={saving}
          >
            {bookingId === 0 ? 'Clear' : 'New'}
          </button>
        </div>
      </form>
    </div>
  );
}

// Helper functions

function getCurrentDateTime(date: Date): string {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(new Date().getHours()).padStart(2, '0');
  const minutes = String(new Date().getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day}T${hours}:${minutes}`;
}

function formatDate(date: Date): string {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

function timeToMinutes(timeStr: string): number {
  const parts = timeStr.split(':');
  const hours = parseInt(parts[0], 10);
  const minutes = parseInt(parts[1], 10) || 0;
  return hours * 60 + minutes;
}

function flattenProjectTree(nodes: any[]): any[] {
  const result: any[] = [];

  function traverse(node: any, depth: number = 0) {
    result.push({
      id: node.id,
      title: node.title,
      path: node.path || node.title,
      depth,
    });

    if (node.children) {
      node.children.forEach((child: any) => traverse(child, depth + 1));
    }
  }

  nodes.forEach((node) => traverse(node));
  return result;
}
