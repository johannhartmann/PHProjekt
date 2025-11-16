/**
 * PHProjekt API Module
 *
 * Centralized API client for React frontend.
 *
 * Usage:
 * ```typescript
 * import { api } from '@/api';
 *
 * // Get timecard bookings
 * const bookings = await api.timecard.getDayBookings('2025-11-16');
 *
 * // Get project tree
 * const tree = await api.project.getProjectTree();
 *
 * // Search
 * const results = await api.search.search('meeting');
 * ```
 */

// Export all types
export * from './types';

// Export API client
export { default as api } from './client';

// Export individual API modules
export {
  timecardApi,
  projectApi,
  tagApi,
  searchApi,
  systemApi,
  configureApiClient,
  ApiError,
  NetworkError,
} from './client';
