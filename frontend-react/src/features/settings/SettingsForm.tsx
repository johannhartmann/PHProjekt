import { useState } from 'react';
import type { FormEvent } from 'react';
import { api, isSuccessResponse } from '@/api';
import type { SettingFieldMetadata, SettingData } from '@/api';
import './SettingsForm.css';

interface SettingsFormProps {
  moduleName: string;
  metadata: SettingFieldMetadata[];
  data: SettingData;
  onSaveSuccess?: () => void;
}

/**
 * Dynamic Settings Form
 *
 * Renders a form based on metadata and handles validation/submission.
 */
export function SettingsForm({
  moduleName,
  metadata,
  data,
  onSaveSuccess,
}: SettingsFormProps) {
  const [formData, setFormData] = useState<SettingData>(data);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [saving, setSaving] = useState(false);
  const [message, setMessage] = useState<{
    type: 'success' | 'error' | 'warning';
    text: string;
  } | null>(null);

  const handleFieldChange = (key: string, value: string | number | boolean) => {
    setFormData((prev) => ({ ...prev, [key]: value }));
    // Clear error for this field
    if (errors[key]) {
      setErrors((prev) => {
        const newErrors = { ...prev };
        delete newErrors[key];
        return newErrors;
      });
    }
  };

  const validateForm = (): boolean => {
    const newErrors: Record<string, string> = {};

    metadata.forEach((field) => {
      const value = formData[field.key];

      // Required field validation
      if (field.required && (value === undefined || value === '' || value === null)) {
        newErrors[field.key] = `${field.label} is required`;
        return;
      }

      // Number range validation
      if (field.type === 'number' && value !== undefined && value !== '') {
        const numValue = Number(value);
        // Check for rowsPerPage specific validation (common in User settings)
        if (field.key === 'rowsPerPage') {
          if (numValue < 5 || numValue > 100) {
            newErrors[field.key] = 'Rows per page must be between 5 and 100';
          }
        }
      }
    });

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setMessage(null);

    if (!validateForm()) {
      setMessage({
        type: 'error',
        text: 'Please fix the errors in the form',
      });
      return;
    }

    setSaving(true);

    try {
      const response = await api.settings.saveSettings({
        moduleName,
        ...formData,
      });

      if (isSuccessResponse(response)) {
        setMessage({
          type: 'success',
          text: response.message || 'Settings saved successfully',
        });

        // Show warning for language changes
        if (moduleName === 'User' && formData.language !== data.language) {
          setTimeout(() => {
            setMessage({
              type: 'warning',
              text: 'You need to log out and log in again for language changes to take effect',
            });
          }, 2000);
        }

        if (onSaveSuccess) {
          onSaveSuccess();
        }
      } else {
        setMessage({
          type: 'error',
          text: response.message || 'Failed to save settings',
        });
      }
    } catch (err) {
      setMessage({
        type: 'error',
        text: err instanceof Error ? err.message : 'An error occurred while saving',
      });
    } finally {
      setSaving(false);
    }
  };

  const handleCancel = () => {
    // Reset form to original data
    setFormData(data);
    setErrors({});
    setMessage(null);
  };

  const renderField = (field: SettingFieldMetadata) => {
    const value = formData[field.key] ?? field.defaultValue ?? '';
    const error = errors[field.key];

    switch (field.type) {
      case 'selectbox':
        return (
          <div key={field.key} className="settings-field">
            <label htmlFor={field.key} className="settings-label">
              {field.label}
              {field.required && <span className="required">*</span>}
            </label>
            <select
              id={field.key}
              value={String(value)}
              onChange={(e) => handleFieldChange(field.key, e.target.value)}
              disabled={field.readOnly || saving}
              className={`settings-select ${error ? 'error' : ''}`}
              required={field.required}
            >
              {field.range?.map((option) => (
                <option key={option.id} value={option.id}>
                  {option.name}
                </option>
              ))}
            </select>
            {field.hint && <span className="settings-hint">{field.hint}</span>}
            {error && <span className="settings-error">{error}</span>}
          </div>
        );

      case 'checkbox':
        return (
          <div key={field.key} className="settings-field settings-field-checkbox">
            <label htmlFor={field.key} className="settings-checkbox-label">
              <input
                id={field.key}
                type="checkbox"
                checked={Boolean(value)}
                onChange={(e) => handleFieldChange(field.key, e.target.checked ? 1 : 0)}
                disabled={field.readOnly || saving}
                className="settings-checkbox"
              />
              <span>{field.label}</span>
              {field.required && <span className="required">*</span>}
            </label>
            {field.hint && <span className="settings-hint">{field.hint}</span>}
            {error && <span className="settings-error">{error}</span>}
          </div>
        );

      case 'number':
        return (
          <div key={field.key} className="settings-field">
            <label htmlFor={field.key} className="settings-label">
              {field.label}
              {field.required && <span className="required">*</span>}
            </label>
            <input
              id={field.key}
              type="number"
              value={String(value)}
              onChange={(e) => handleFieldChange(field.key, e.target.value)}
              disabled={field.readOnly || saving}
              className={`settings-input ${error ? 'error' : ''}`}
              required={field.required}
            />
            {field.hint && <span className="settings-hint">{field.hint}</span>}
            {error && <span className="settings-error">{error}</span>}
          </div>
        );

      case 'textarea':
        return (
          <div key={field.key} className="settings-field">
            <label htmlFor={field.key} className="settings-label">
              {field.label}
              {field.required && <span className="required">*</span>}
            </label>
            <textarea
              id={field.key}
              value={String(value)}
              onChange={(e) => handleFieldChange(field.key, e.target.value)}
              disabled={field.readOnly || saving}
              className={`settings-textarea ${error ? 'error' : ''}`}
              required={field.required}
              rows={4}
            />
            {field.hint && <span className="settings-hint">{field.hint}</span>}
            {error && <span className="settings-error">{error}</span>}
          </div>
        );

      default: // text
        return (
          <div key={field.key} className="settings-field">
            <label htmlFor={field.key} className="settings-label">
              {field.label}
              {field.required && <span className="required">*</span>}
            </label>
            <input
              id={field.key}
              type="text"
              value={String(value)}
              onChange={(e) => handleFieldChange(field.key, e.target.value)}
              disabled={field.readOnly || saving}
              className={`settings-input ${error ? 'error' : ''}`}
              required={field.required}
            />
            {field.hint && <span className="settings-hint">{field.hint}</span>}
            {error && <span className="settings-error">{error}</span>}
          </div>
        );
    }
  };

  return (
    <form onSubmit={handleSubmit} className="settings-form">
      <h2 className="settings-form-title">{moduleName} Settings</h2>

      {message && (
        <div className={`settings-message settings-message-${message.type}`}>
          {message.text}
        </div>
      )}

      <div className="settings-fields">
        {metadata.map((field) => renderField(field))}
      </div>

      <div className="settings-actions">
        <button
          type="submit"
          disabled={saving}
          className="settings-button settings-button-primary"
        >
          {saving ? 'Saving...' : 'Save Settings'}
        </button>
        <button
          type="button"
          onClick={handleCancel}
          disabled={saving}
          className="settings-button settings-button-secondary"
        >
          Cancel
        </button>
      </div>
    </form>
  );
}
