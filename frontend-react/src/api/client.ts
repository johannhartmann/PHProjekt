/**
 * PHProjekt API Client
 *
 * Centralized HTTP client for all API calls to PHProjekt backend.
 * Handles:
 * - Session cookies (automatic)
 * - CSRF token (if needed)
 * - Common error handling
 * - Request/response transformation
 */

import type {
  TimecardBooking,
  TimecardDayListResponse,
  TimecardFavoriteProject,
  TimecardRunningBookingResponse,
  TimecardMonthDaySummary,
  TimecardMonthListResponse,
  Project,
  ProjectListResponse,
  ProjectTreeNode,
  ProjectModulePermission,
  ProjectRoleUserRelation,
  SaveResponse,
  DeleteResponse,
  Tag,
  SearchResult,
  SettingModule,
  SettingDetailResponse,
  SaveSettingsRequest,
  FrontendConfig,
  FrontendMessage,
  ApiResponse,
} from './types';

// ============================================================================
// Configuration
// ============================================================================

/**
 * API Base URL
 * Defaults to /index.php for compatibility with existing backend
 */
const API_BASE_URL = '/index.php';

/**
 * API Client configuration
 */
interface ApiClientConfig {
  /** Base URL for API requests */
  baseUrl?: string;

  /** CSRF token (if using header-based CSRF) */
  csrfToken?: string;

  /** Custom headers */
  headers?: Record<string, string>;

  /** Request timeout (ms) */
  timeout?: number;
}

/**
 * Global API client config
 */
let config: ApiClientConfig = {
  baseUrl: API_BASE_URL,
  timeout: 30000, // 30 seconds
};

/**
 * Configure the API client
 */
export function configureApiClient(newConfig: Partial<ApiClientConfig>): void {
  config = { ...config, ...newConfig };
}

// ============================================================================
// Error Handling
// ============================================================================

/**
 * API Error class
 */
export class ApiError extends Error {
  statusCode?: number;
  response?: unknown;

  constructor(message: string, statusCode?: number, response?: unknown) {
    super(message);
    this.name = 'ApiError';
    this.statusCode = statusCode;
    this.response = response;
  }
}

/**
 * Network error class
 */
export class NetworkError extends Error {
  originalError?: Error;

  constructor(message: string, originalError?: Error) {
    super(message);
    this.name = 'NetworkError';
    this.originalError = originalError;
  }
}

// ============================================================================
// Core HTTP Client
// ============================================================================

/**
 * Make an HTTP request
 */
async function request<T>(
  endpoint: string,
  options: RequestInit = {}
): Promise<T> {
  const url = `${config.baseUrl}${endpoint}`;

  // Build headers
  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
    ...config.headers,
    ...((options.headers || {}) as Record<string, string>),
  };

  // Add CSRF token if configured
  if (config.csrfToken) {
    headers['X-CSRFToken'] = config.csrfToken;
  }

  // Build request options
  const requestOptions: RequestInit = {
    ...options,
    headers,
    credentials: 'same-origin', // Send cookies for session
  };

  try {
    // Make request with timeout
    const controller = new AbortController();
    const timeoutId = setTimeout(
      () => controller.abort(),
      config.timeout || 30000
    );

    const response = await fetch(url, {
      ...requestOptions,
      signal: controller.signal,
    });

    clearTimeout(timeoutId);

    // Handle non-2xx responses
    if (!response.ok) {
      const errorText = await response.text();
      throw new ApiError(
        `HTTP ${response.status}: ${response.statusText}`,
        response.status,
        errorText
      );
    }

    // Parse JSON response
    const data = await response.json();

    return data as T;
  } catch (error) {
    // Handle timeout/network errors
    if (error instanceof DOMException && error.name === 'AbortError') {
      throw new NetworkError('Request timeout', error as Error);
    }

    // Handle fetch errors (network issues)
    if (error instanceof TypeError) {
      throw new NetworkError('Network error', error);
    }

    // Re-throw API errors
    if (error instanceof ApiError) {
      throw error;
    }

    // Unknown error
    throw new ApiError('Unknown error occurred', undefined, error);
  }
}

/**
 * Make a GET request
 */
async function get<T>(
  endpoint: string,
  params?: Record<string, string | number>
): Promise<T> {
  let url = endpoint;

  if (params) {
    const searchParams = new URLSearchParams();
    Object.entries(params).forEach(([key, value]) => {
      searchParams.append(key, String(value));
    });
    url += `?${searchParams.toString()}`;
  }

  return request<T>(url, { method: 'GET' });
}

/**
 * Make a POST request
 */
async function post<T>(
  endpoint: string,
  data?: Record<string, unknown>
): Promise<T> {
  const body = data ? JSON.stringify(data) : undefined;

  return request<T>(endpoint, {
    method: 'POST',
    body,
  });
}

// ============================================================================
// Timecard API
// ============================================================================

export const timecardApi = {
  /**
   * Get bookings for a specific day
   *
   * @param date - Date in YYYY-MM-DD format
   * @returns Array of bookings for the day
   */
  async getDayBookings(date: string): Promise<TimecardBooking[]> {
    const response = await get<TimecardDayListResponse>(
      `/Timecard/index/jsonDayList`,
      { date }
    );
    return response.data || [];
  },

  /**
   * Get favorite projects
   *
   * @returns Array of favorite projects
   */
  async getFavoriteProjects(): Promise<TimecardFavoriteProject[]> {
    const response = await get<TimecardFavoriteProject[]>(
      `/Timecard/index/jsonGetFavoritesProjects`
    );
    return response || [];
  },

  /**
   * Get currently running booking
   *
   * @param year - Year
   * @param month - Month (1-12)
   * @param date - Day of month
   * @returns Running booking or null
   */
  async getRunningBooking(
    year?: number,
    month?: number,
    date?: number
  ): Promise<TimecardRunningBookingResponse> {
    const now = new Date();
    const params: Record<string, number> = {
      year: year || now.getFullYear(),
      month: month || now.getMonth() + 1,
      date: date || now.getDate(),
    };

    return get<TimecardRunningBookingResponse>(
      `/Timecard/index/jsonGetRunningBookings`,
      params
    );
  },

  /**
   * Save a timecard booking
   *
   * @param id - Booking ID (0 for new, >0 for update)
   * @param booking - Booking data
   * @returns Save response
   */
  async saveBooking(
    id: number,
    booking: {
      startDatetime: string;
      endTime?: string | null;
      projectId: number;
      notes?: string;
      timecardId?: number;
    }
  ): Promise<SaveResponse> {
    return post<SaveResponse>(`/Timecard/index/jsonSave/nodeId/1/id/${id}`, booking);
  },

  /**
   * Delete a timecard booking
   *
   * @param id - Booking ID
   * @returns Delete response
   */
  async deleteBooking(id: number): Promise<DeleteResponse> {
    return post<DeleteResponse>(`/Timecard/index/jsonDelete/id/${id}`, {});
  },

  /**
   * Get monthly summary of booked hours
   *
   * @param year - Year (YYYY)
   * @param month - Month (1-12)
   * @returns Monthly summary data
   */
  async getMonthSummary(
    year: number,
    month: number
  ): Promise<TimecardMonthDaySummary[]> {
    const response = await get<TimecardMonthListResponse>(
      `/Timecard/index/jsonMonthList/year/${year}/month/${month}`
    );
    return response.data || [];
  },

  /**
   * Export bookings to CSV
   *
   * @param year - Year (YYYY)
   * @param month - Month (1-12)
   * @param csrfToken - CSRF token for security
   */
  exportToCSV(year: number, month: number, csrfToken: string): void {
    const url = `/index.php/Timecard/index/csvList/nodeId/1/year/${year}/month/${month}/csrfToken/${csrfToken}`;
    window.open(url, '_blank');
  },
};

// ============================================================================
// Project API
// ============================================================================

export const projectApi = {
  /**
   * Get project list
   *
   * @param parentId - Parent project ID (nodeId)
   * @param options - Additional query options
   * @returns Project list response
   */
  async getProjects(
    parentId: number = 1,
    options?: { count?: number; start?: number; recursive?: boolean }
  ): Promise<ProjectListResponse> {
    const params: Record<string, string | number> = {
      nodeId: parentId,
    };

    if (options?.count) params.count = options.count;
    if (options?.start) params.start = options.start;
    if (options?.recursive) params.recursive = 'true';

    return get<ProjectListResponse>(`/Project/index/jsonList`, params);
  },

  /**
   * Get project tree
   *
   * @returns Full project tree
   */
  async getProjectTree(): Promise<ProjectTreeNode[]> {
    const response = await get<ProjectTreeNode[]>(`/Project/index/jsonTree`);
    return response || [];
  },

  /**
   * Get project by ID
   *
   * @param projectId - Project ID
   * @param nodeId - Node/parent ID
   * @returns Project details
   */
  async getProject(projectId: number, nodeId: number = 1): Promise<Project> {
    return get<Project>(`/Project/index/jsonDetail`, {
      id: projectId,
      nodeId,
    });
  },

  /**
   * Save a project
   *
   * @param project - Project data
   * @returns Save response
   */
  async saveProject(
    project: Partial<Project> & { projectId: number }
  ): Promise<SaveResponse> {
    return post<SaveResponse>(`/Project/index/jsonSave`, project);
  },

  /**
   * Delete a project
   *
   * @param id - Project ID
   * @returns Delete response
   */
  async deleteProject(id: number): Promise<DeleteResponse> {
    return post<DeleteResponse>(`/Default/index/jsonDelete`, { id });
  },

  /**
   * Get module permissions for a project
   *
   * @param projectId - Project ID
   * @param nodeId - Parent node ID
   * @returns Array of module permissions
   */
  async getModulePermissions(
    projectId: number,
    nodeId: number = 1
  ): Promise<ProjectModulePermission[]> {
    return get<ProjectModulePermission[]>(
      `/Project/index/jsonGetModulesProjectRelation`,
      { id: projectId, nodeId }
    );
  },

  /**
   * Get role-user relations for a project
   *
   * @param projectId - Project ID
   * @param nodeId - Parent node ID
   * @returns Array of role-user relations
   */
  async getRoleUserRelations(
    projectId: number,
    nodeId: number = 1
  ): Promise<ProjectRoleUserRelation[]> {
    return get<ProjectRoleUserRelation[]>(
      `/Project/index/jsonGetProjectRoleUserRelation`,
      { id: projectId, nodeId }
    );
  },
};

// ============================================================================
// Tag API (Cross-Module)
// ============================================================================

export const tagApi = {
  /**
   * Get all tags
   *
   * @returns Array of tags
   */
  async getAllTags(): Promise<Tag[]> {
    return get<Tag[]>(`/Default/Tag/jsonGetTags`);
  },

  /**
   * Get tags for a specific module and item
   *
   * @param moduleName - Module name (e.g., "Project", "Timecard")
   * @param itemId - Item ID
   * @returns Array of tags
   */
  async getTagsForItem(moduleName: string, itemId: number): Promise<Tag[]> {
    return get<Tag[]>(`/Default/Tag/jsonGetTagsByModule`, {
      moduleName,
      id: itemId,
    });
  },

  /**
   * Save tags for a specific module and item
   *
   * @param moduleName - Module name
   * @param itemId - Item ID
   * @param tags - Comma-separated tag string
   * @returns Save response
   */
  async saveTags(
    moduleName: string,
    itemId: number,
    tags: string
  ): Promise<SaveResponse> {
    return post<SaveResponse>(`/Default/Tag/jsonSaveTags`, {
      moduleName,
      id: itemId,
      string: tags,
    });
  },

  /**
   * Delete tags for a specific module and item
   *
   * @param moduleName - Module name
   * @param itemId - Item ID
   * @returns Delete response
   */
  async deleteTags(
    moduleName: string,
    itemId: number
  ): Promise<DeleteResponse> {
    return post<DeleteResponse>(`/Default/Tag/jsonDeleteTags`, {
      moduleName,
      id: itemId,
    });
  },
};

// ============================================================================
// Search API
// ============================================================================

export const searchApi = {
  /**
   * Perform full-text search
   *
   * @param query - Search query
   * @returns Array of search results
   */
  async search(query: string): Promise<SearchResult[]> {
    return get<SearchResult[]>(`/Default/Search/jsonSearch`, { query });
  },
};

// ============================================================================
// Settings API
// ============================================================================

export const settingsApi = {
  /**
   * Get list of available setting modules
   *
   * @returns Array of setting modules
   */
  async getModules(): Promise<SettingModule[]> {
    return get<SettingModule[]>(`/Core/Setting/jsonGetModules`);
  },

  /**
   * Get settings for a specific module
   *
   * @param moduleName - Name of the module (e.g., "User")
   * @returns Setting detail response with metadata and data
   */
  async getModuleSettings(moduleName: string): Promise<SettingDetailResponse> {
    return get<SettingDetailResponse>(`/Core/Setting/jsonDetail`, {
      moduleName,
    });
  },

  /**
   * Save settings for a module
   *
   * @param settings - Settings to save (must include moduleName)
   * @returns Save response
   */
  async saveSettings(settings: SaveSettingsRequest): Promise<ApiResponse> {
    return post<ApiResponse>(`/Core/Setting/jsonSave`, settings);
  },
};

// ============================================================================
// System API
// ============================================================================

export const systemApi = {
  /**
   * Get frontend configuration
   *
   * @returns Frontend configuration
   */
  async getConfig(): Promise<FrontendConfig> {
    return get<FrontendConfig>(`/Default/index/jsonGetConfigurations`);
  },

  /**
   * Get frontend messages/notifications
   *
   * @returns Array of frontend messages
   */
  async getFrontendMessages(): Promise<FrontendMessage[]> {
    return get<FrontendMessage[]>(`/Default/index/jsonGetFrontendMessage`);
  },

  /**
   * Disable frontend messages
   *
   * @returns Response
   */
  async disableFrontendMessages(): Promise<ApiResponse> {
    return post<ApiResponse>(`/Default/index/jsonDisableFrontendMessages`);
  },

  /**
   * Get translated strings for a language
   *
   * @param language - Language code (e.g., "en", "de")
   * @returns Translation object
   */
  async getTranslations(language: string): Promise<Record<string, string>> {
    return get<Record<string, string>>(
      `/Default/index/jsonGetTranslatedStrings`,
      { language }
    );
  },
};

// ============================================================================
// Export All APIs
// ============================================================================

/**
 * Main API client export
 */
export const api = {
  timecard: timecardApi,
  project: projectApi,
  tag: tagApi,
  search: searchApi,
  settings: settingsApi,
  system: systemApi,
};

/**
 * Export default API client
 */
export default api;
