/**
 * TypeScript Types for PHProjekt API
 *
 * These types are inferred from the existing PHP endpoints:
 * - Timecard/index/jsonDayList
 * - Project/index/jsonList
 * - Project/index/jsonTree
 */

// ============================================================================
// Common API Response Types
// ============================================================================

/**
 * Standard API success response
 */
export interface ApiSuccessResponse<T = unknown> {
  type: 'success';
  message?: string;
  data?: T;
  id?: number | string;
}

/**
 * Standard API error response
 */
export interface ApiErrorResponse {
  type: 'error';
  message: string;
  code?: string;
  id?: number | string;
}

/**
 * Union type for API responses
 */
export type ApiResponse<T = unknown> = ApiSuccessResponse<T> | ApiErrorResponse;

/**
 * Type guard for success responses
 */
export function isSuccessResponse<T>(
  response: ApiResponse<T>
): response is ApiSuccessResponse<T> {
  return response.type === 'success';
}

/**
 * Type guard for error responses
 */
export function isErrorResponse(
  response: ApiResponse
): response is ApiErrorResponse {
  return response.type === 'error';
}

// ============================================================================
// Timecard Module Types
// ============================================================================

/**
 * Timecard booking record
 *
 * Endpoint: GET /index.php/Timecard/index/jsonDayList?date={date}
 * Source: Timecard_Models_Timecard::getDayRecords()
 */
export interface TimecardBooking {
  /** Booking ID */
  id: number;

  /** Project ID */
  projectId: number;

  /** Start time (HH:MM:SS format) */
  startTime: string;

  /** End time (HH:MM:SS format, null if booking is running) */
  endTime: string | null;

  /** Project display name with depth indentation */
  display: string;

  /** Booking notes (truncated to 50 chars in list view) */
  note: string;
}

/**
 * Response from jsonDayList endpoint
 */
export interface TimecardDayListResponse {
  data: TimecardBooking[];
}

/**
 * Favorite project info
 *
 * Endpoint: GET /index.php/Timecard/index/jsonGetFavoritesProjects
 * Source: Timecard/IndexController::jsonGetFavoritesProjectsAction()
 */
export interface TimecardFavoriteProject {
  /** Project ID */
  id: number;

  /** Project display name with depth */
  display: string;

  /** Project name without depth */
  name: string;
}

/**
 * Running booking info
 *
 * Endpoint: GET /index.php/Timecard/index/jsonGetRunningBookings
 * Source: Timecard/IndexController::jsonGetRunningBookingsAction()
 */
export interface TimecardRunningBooking {
  id: number;
  projectId: number;
  startTime: string;
  endTime: string | null;
  note: string;
}

/**
 * Response from jsonGetRunningBookings endpoint
 */
export interface TimecardRunningBookingResponse {
  type: 'success';
  data: TimecardRunningBooking | null;
  id: number;
}

// ============================================================================
// Project Module Types
// ============================================================================

/**
 * Project record from list
 *
 * Endpoint: GET /index.php/Project/index/jsonList?nodeId={parentId}
 * Source: Default/IndexController::jsonListAction()
 *
 * Note: Actual fields depend on Project model definition
 * This is a base interface - extend as needed
 */
export interface Project {
  /** Project ID */
  id: number;

  /** Project title/name */
  title: string;

  /** Parent project ID */
  projectId: number;

  /** Project path (e.g., "/1/2/3") */
  path: string;

  /** Project start date (ISO 8601) */
  startDate: string | null;

  /** Project end date (ISO 8601) */
  endDate: string | null;

  /** Project priority (1-10) */
  priority: number;

  /** Project status */
  status: number;

  /** Complete percentage (0-100) */
  completePercent: number;

  /** Budget hours */
  budget: number | null;

  /** Project notes */
  notes: string;

  /** Owner user ID */
  ownerId: number;

  /** Creation datetime */
  createdAt: string;

  /** Last modification datetime */
  modifiedAt: string;
}

/**
 * Project tree node
 *
 * Endpoint: GET /index.php/Project/index/jsonTree
 * Source: Phprojekt_Tree_Node_Database::setup()
 */
export interface ProjectTreeNode {
  /** Project ID */
  id: number;

  /** Project title */
  title: string;

  /** Parent project ID (null for root) */
  parent: number | null;

  /** Tree path */
  path: string;

  /** Child projects */
  children?: ProjectTreeNode[];

  /** Project metadata (extends Project interface) */
  data?: Partial<Project>;
}

/**
 * Project list response
 *
 * Note: PHProjekt uses Phprojekt_Converter_Json which outputs
 * structured data with metadata and numRows
 */
export interface ProjectListResponse {
  /** Project metadata schema */
  metadata?: ProjectMetadata[];

  /** Array of projects */
  data: Project[];

  /** Total number of rows */
  numRows?: number;
}

/**
 * Field metadata from PHProjekt
 */
export interface ProjectMetadata {
  /** Field key */
  key: string;

  /** Field label (translated) */
  label: string;

  /** Field type (text, textarea, selectbox, date, etc.) */
  type: string;

  /** Field hint/help text */
  hint?: string;

  /** Is field required? */
  required?: boolean;

  /** Is field read-only? */
  readOnly?: boolean;

  /** Select options (for selectbox type) */
  range?: Record<string, string>;

  /** Default value */
  default?: string | number;
}

/**
 * Project module permissions
 *
 * Endpoint: GET /index.php/Project/index/jsonGetModulesProjectRelation
 * Source: Project/IndexController::jsonGetModulesProjectRelationAction()
 */
export interface ProjectModulePermission {
  /** Module ID */
  id: number;

  /** Module name */
  name: string;

  /** Module label (translated) */
  label: string;

  /** Is module active in this project? */
  inProject: boolean;
}

/**
 * Project role-user relation
 *
 * Endpoint: GET /index.php/Project/index/jsonGetProjectRoleUserRelation
 * Source: Project/IndexController::jsonGetProjectRoleUserRelationAction()
 */
export interface ProjectRoleUserRelation {
  /** Role ID */
  id: number;

  /** Role name */
  name: string;

  /** Users with this role */
  users: Array<{
    id: number;
    display: string;
  }>;
}

// ============================================================================
// Save/Delete Operation Types
// ============================================================================

/**
 * Save operation result
 */
export interface SaveResponse {
  type: 'success' | 'error';
  message: string;
  id: number | string;
}

/**
 * Delete operation result
 */
export interface DeleteResponse {
  type: 'success' | 'error';
  message: string;
  id: number | string;
}

// ============================================================================
// Search and Tag Types (Cross-Module)
// ============================================================================

/**
 * Tag record
 *
 * Endpoint: GET /index.php/Default/Tag/jsonGetTags
 */
export interface Tag {
  id: number;
  string: string;
  count?: number;
}

/**
 * Search result
 *
 * Endpoint: GET /index.php/Default/Search/jsonSearch
 */
export interface SearchResult {
  /** Module name */
  moduleName: string;

  /** Module label */
  moduleLabel: string;

  /** Item ID */
  itemId: number;

  /** Project ID */
  projectId: number;

  /** First display field */
  firstDisplay: string;

  /** Second display field */
  secondDisplay: string;

  /** Third display field */
  thirdDisplay: string;

  /** Match score/relevance */
  score?: number;
}

// ============================================================================
// Settings Types
// ============================================================================

/**
 * Setting module information
 * Endpoint: GET /index.php/Core/Setting/jsonGetModules
 */
export interface SettingModule {
  name: string;
  label: string;
}

/**
 * Setting field metadata (describes a form field)
 */
export interface SettingFieldMetadata {
  key: string;
  label: string;
  type: 'text' | 'selectbox' | 'checkbox' | 'number' | 'textarea';
  range?: Array<{ id: string; name: string }>;
  required?: boolean;
  readOnly?: boolean;
  hint?: string;
  defaultValue?: string | number | boolean;
}

/**
 * Setting data (key-value pairs for actual settings values)
 */
export interface SettingData {
  [key: string]: string | number | boolean;
}

/**
 * Setting detail response
 * Endpoint: GET /index.php/Core/Setting/jsonDetail?moduleName=User
 */
export interface SettingDetailResponse {
  metadata: SettingFieldMetadata[];
  data: SettingData[];
  numRows: number;
}

/**
 * Save settings request
 * Endpoint: POST /index.php/Core/Setting/jsonSave
 */
export interface SaveSettingsRequest {
  moduleName: string;
  [key: string]: string | number | boolean;
}

// ============================================================================
// Configuration and System Types
// ============================================================================

/**
 * Frontend configuration
 *
 * Endpoint: GET /index.php/Default/index/jsonGetConfigurations
 */
export interface FrontendConfig {
  /** Application version */
  version: string;

  /** User timezone */
  timezone: string;

  /** Date format */
  dateFormat: string;

  /** Time format */
  timeFormat: string;

  /** Datetime format */
  datetimeFormat: string;

  /** Locale */
  locale: string;

  /** CSRF token (may be in headers instead) */
  csrfToken?: string;

  /** Other config values */
  [key: string]: string | number | boolean | undefined;
}

/**
 * Frontend message/notification
 *
 * Endpoint: GET /index.php/Default/index/jsonGetFrontendMessage
 */
export interface FrontendMessage {
  /** Message ID */
  id: number;

  /** Message text */
  message: string;

  /** Message type (info, warning, error, success) */
  type: 'info' | 'warning' | 'error' | 'success';

  /** Creation timestamp */
  created: string;
}

// ============================================================================
// User and Authentication Types
// ============================================================================

/**
 * User record
 *
 * Source: Core/User module
 */
export interface User {
  id: number;
  username: string;
  firstname: string;
  lastname: string;
  email: string;
  display?: string;
  admin: boolean;
}

/**
 * Current user session info
 */
export interface SessionInfo {
  userId: number;
  username: string;
  isAuthenticated: boolean;
  admin: boolean;
}
