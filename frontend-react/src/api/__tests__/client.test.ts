/**
 * API Client Unit Tests
 *
 * Tests for the PHProjekt API client including:
 * - HTTP methods (GET, POST)
 * - Error handling
 * - Query parameter building
 * - CSRF token handling
 * - Timeout handling
 */

import { describe, it, expect, beforeEach, vi } from 'vitest';
import {
  timecardApi,
  projectApi,
  tagApi,
  searchApi,
  systemApi,
  configureApiClient,
  ApiError,
  NetworkError,
} from '../client';

// Mock fetch globally
const mockFetch = vi.fn();
global.fetch = mockFetch;

describe('API Client', () => {
  beforeEach(() => {
    // Reset mocks before each test
    mockFetch.mockReset();

    // Reset config to defaults
    configureApiClient({
      baseUrl: '/index.php',
      timeout: 30000,
      csrfToken: undefined,
    });
  });

  describe('HTTP Client Core', () => {
    it('should make GET requests with correct URL', async () => {
      const mockData = { data: [{ id: 1, name: 'Test' }] };
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockData,
      });

      await timecardApi.getDayBookings('2025-11-16');

      expect(mockFetch).toHaveBeenCalledWith(
        '/index.php/Timecard/index/jsonDayList?date=2025-11-16',
        expect.objectContaining({
          method: 'GET',
          credentials: 'same-origin',
        })
      );
    });

    it('should make POST requests with JSON body', async () => {
      const mockResponse = { type: 'success', message: 'Saved', id: 123 };
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockResponse,
      });

      const bookingData = {
        id: 1,
        projectId: 10,
        startDatetime: '2025-11-16 09:00:00',
      };

      await timecardApi.saveBooking(bookingData);

      expect(mockFetch).toHaveBeenCalledWith(
        '/index.php/Timecard/index/jsonSave',
        expect.objectContaining({
          method: 'POST',
          body: JSON.stringify(bookingData),
          headers: expect.objectContaining({
            'Content-Type': 'application/json',
          }),
        })
      );
    });

    it('should include session credentials', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({}),
      });

      await projectApi.getProjectTree();

      expect(mockFetch).toHaveBeenCalledWith(
        expect.any(String),
        expect.objectContaining({
          credentials: 'same-origin',
        })
      );
    });

    it('should add CSRF token to headers when configured', async () => {
      configureApiClient({ csrfToken: 'test-token-123' });

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({}),
      });

      await projectApi.getProjectTree();

      expect(mockFetch).toHaveBeenCalledWith(
        expect.any(String),
        expect.objectContaining({
          headers: expect.objectContaining({
            'X-CSRFToken': 'test-token-123',
          }),
        })
      );
    });
  });

  describe('Query Parameters', () => {
    it('should build query parameters correctly', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({ data: [] }),
      });

      await projectApi.getProjects(5, { count: 20, start: 10, recursive: true });

      expect(mockFetch).toHaveBeenCalledWith(
        '/index.php/Project/index/jsonList?nodeId=5&count=20&start=10&recursive=true',
        expect.any(Object)
      );
    });

    it('should handle special characters in parameters', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ([]),
      });

      await searchApi.search('test & query');

      expect(mockFetch).toHaveBeenCalledWith(
        '/index.php/Default/Search/jsonSearch?query=test+%26+query',
        expect.any(Object)
      );
    });
  });

  describe('Error Handling', () => {
    it('should throw ApiError on HTTP 404', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: false,
        status: 404,
        statusText: 'Not Found',
        text: async () => 'Resource not found',
      });

      await expect(projectApi.getProject(999)).rejects.toThrow(ApiError);

      // Set up mock again for second call
      mockFetch.mockResolvedValueOnce({
        ok: false,
        status: 404,
        statusText: 'Not Found',
        text: async () => 'Resource not found',
      });

      await expect(projectApi.getProject(999)).rejects.toThrow('HTTP 404: Not Found');
    });

    it('should throw ApiError on HTTP 500', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: false,
        status: 500,
        statusText: 'Internal Server Error',
        text: async () => 'Server error',
      });

      await expect(timecardApi.getDayBookings('2025-11-16')).rejects.toThrow(ApiError);
    });

    it('should include status code in ApiError', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: false,
        status: 403,
        statusText: 'Forbidden',
        text: async () => 'Access denied',
      });

      try {
        await projectApi.deleteProject(1);
        expect.fail('Should have thrown ApiError');
      } catch (error) {
        expect(error).toBeInstanceOf(ApiError);
        expect((error as ApiError).statusCode).toBe(403);
      }
    });

    it('should throw NetworkError on timeout', async () => {
      configureApiClient({ timeout: 100 });

      // Mock fetch to check if it receives AbortSignal and simulate abort
      mockFetch.mockImplementationOnce(
        (_url, options) =>
          new Promise((_resolve, reject) => {
            const signal = options?.signal as AbortSignal;
            if (signal) {
              // Listen for abort event
              signal.addEventListener('abort', () => {
                reject(new DOMException('Aborted', 'AbortError'));
              });
            }
            // Simulate long request (never resolve)
          })
      );

      await expect(projectApi.getProjectTree()).rejects.toThrow(NetworkError);
    });

    it('should throw NetworkError on network failure', async () => {
      mockFetch.mockRejectedValueOnce(new TypeError('Failed to fetch'));

      await expect(projectApi.getProjectTree()).rejects.toThrow(NetworkError);
      await expect(projectApi.getProjectTree()).rejects.toThrow('Network error');
    });

    it('should handle JSON parse errors', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => {
          throw new SyntaxError('Invalid JSON');
        },
      });

      await expect(projectApi.getProjectTree()).rejects.toThrow();
    });
  });

  describe('Timecard API', () => {
    it('should get day bookings', async () => {
      const mockBookings = {
        data: [
          {
            id: 1,
            projectId: 10,
            startTime: '09:00:00',
            endTime: '17:00:00',
            display: 'Project A',
            note: 'Work on feature X',
          },
        ],
      };

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockBookings,
      });

      const result = await timecardApi.getDayBookings('2025-11-16');

      expect(result).toEqual(mockBookings.data);
      expect(result[0].id).toBe(1);
      expect(result[0].projectId).toBe(10);
    });

    it('should handle empty day bookings', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({ data: [] }),
      });

      const result = await timecardApi.getDayBookings('2025-11-16');

      expect(result).toEqual([]);
    });

    it('should get favorite projects', async () => {
      const mockFavorites = [
        { id: 1, display: 'Project A', name: 'Project A' },
        { id: 2, display: '  Project B', name: 'Project B' },
      ];

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockFavorites,
      });

      const result = await timecardApi.getFavoriteProjects();

      expect(result).toEqual(mockFavorites);
      expect(result).toHaveLength(2);
    });

    it('should get running booking', async () => {
      const mockRunning = {
        type: 'success',
        data: {
          id: 5,
          projectId: 10,
          startTime: '14:30:00',
          endTime: null,
          note: 'Currently working',
        },
        id: 0,
      };

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockRunning,
      });

      const result = await timecardApi.getRunningBooking();

      expect(result.type).toBe('success');
      expect(result.data?.id).toBe(5);
      expect(result.data?.endTime).toBeNull();
    });

    it('should use current date for running booking when no params', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({ type: 'success', data: null, id: 0 }),
      });

      const now = new Date();
      await timecardApi.getRunningBooking();

      const callUrl = mockFetch.mock.calls[0][0] as string;
      expect(callUrl).toContain(`year=${now.getFullYear()}`);
      expect(callUrl).toContain(`month=${now.getMonth() + 1}`);
    });
  });

  describe('Project API', () => {
    it('should get project tree', async () => {
      const mockTree = [
        {
          id: 1,
          title: 'Root Project',
          parent: null,
          path: '/1',
          children: [
            {
              id: 2,
              title: 'Child Project',
              parent: 1,
              path: '/1/2',
            },
          ],
        },
      ];

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockTree,
      });

      const result = await projectApi.getProjectTree();

      expect(result).toEqual(mockTree);
      expect(result[0].title).toBe('Root Project');
      expect(result[0].children).toHaveLength(1);
    });

    it('should get projects with default parent', async () => {
      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({ data: [] }),
      });

      await projectApi.getProjects();

      const callUrl = mockFetch.mock.calls[0][0] as string;
      expect(callUrl).toContain('nodeId=1');
    });

    it('should get project by ID', async () => {
      const mockProject = {
        id: 5,
        title: 'Test Project',
        projectId: 1,
        path: '/1/5',
        status: 1,
      };

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockProject,
      });

      const result = await projectApi.getProject(5);

      expect(result.id).toBe(5);
      expect(result.title).toBe('Test Project');
    });

    it('should save project', async () => {
      const mockResponse = {
        type: 'success',
        message: 'Project saved',
        id: 10,
      };

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockResponse,
      });

      const projectData = {
        title: 'New Project',
        projectId: 1,
        status: 1,
      };

      const result = await projectApi.saveProject(projectData);

      expect(result.type).toBe('success');
      expect(result.id).toBe(10);
    });
  });

  describe('Tag API', () => {
    it('should get all tags', async () => {
      const mockTags = [
        { id: 1, string: 'important', count: 5 },
        { id: 2, string: 'urgent', count: 3 },
      ];

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockTags,
      });

      const result = await tagApi.getAllTags();

      expect(result).toHaveLength(2);
      expect(result[0].string).toBe('important');
    });

    it('should get tags for specific item', async () => {
      const mockTags = [{ id: 1, string: 'project-tag' }];

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockTags,
      });

      const result = await tagApi.getTagsForItem('Project', 5);

      expect(result).toHaveLength(1);
      expect(mockFetch).toHaveBeenCalledWith(
        expect.stringContaining('moduleName=Project'),
        expect.any(Object)
      );
      expect(mockFetch).toHaveBeenCalledWith(
        expect.stringContaining('id=5'),
        expect.any(Object)
      );
    });

    it('should save tags', async () => {
      const mockResponse = {
        type: 'success',
        message: 'Tags saved',
        id: 5,
      };

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockResponse,
      });

      const result = await tagApi.saveTags('Project', 5, 'tag1,tag2,tag3');

      expect(result.type).toBe('success');
      expect(mockFetch).toHaveBeenCalledWith(
        expect.any(String),
        expect.objectContaining({
          method: 'POST',
          body: expect.stringContaining('tag1,tag2,tag3'),
        })
      );
    });
  });

  describe('Search API', () => {
    it('should perform search', async () => {
      const mockResults = [
        {
          moduleName: 'Project',
          moduleLabel: 'Projects',
          itemId: 1,
          projectId: 1,
          firstDisplay: 'Project A',
          secondDisplay: 'Description',
          thirdDisplay: '2025-11-16',
        },
      ];

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockResults,
      });

      const result = await searchApi.search('test query');

      expect(result).toHaveLength(1);
      expect(result[0].moduleName).toBe('Project');
      expect(mockFetch).toHaveBeenCalledWith(
        expect.stringContaining('query=test+query'),
        expect.any(Object)
      );
    });
  });

  describe('System API', () => {
    it('should get config', async () => {
      const mockConfig = {
        version: '6.2.0',
        timezone: 'Europe/Berlin',
        dateFormat: 'Y-m-d',
        timeFormat: 'H:i',
      };

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockConfig,
      });

      const result = await systemApi.getConfig();

      expect(result.version).toBe('6.2.0');
      expect(result.timezone).toBe('Europe/Berlin');
    });

    it('should get translations', async () => {
      const mockTranslations = {
        'Login': 'Anmelden',
        'Logout': 'Abmelden',
      };

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => mockTranslations,
      });

      const result = await systemApi.getTranslations('de');

      expect(result['Login']).toBe('Anmelden');
      expect(mockFetch).toHaveBeenCalledWith(
        expect.stringContaining('language=de'),
        expect.any(Object)
      );
    });
  });

  describe('Configuration', () => {
    it('should allow custom base URL', async () => {
      configureApiClient({ baseUrl: '/api/v1' });

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({}),
      });

      await projectApi.getProjectTree();

      expect(mockFetch).toHaveBeenCalledWith(
        expect.stringContaining('/api/v1'),
        expect.any(Object)
      );
    });

    it('should allow custom headers', async () => {
      configureApiClient({
        headers: {
          'X-Custom-Header': 'custom-value',
        },
      });

      mockFetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({}),
      });

      await projectApi.getProjectTree();

      expect(mockFetch).toHaveBeenCalledWith(
        expect.any(String),
        expect.objectContaining({
          headers: expect.objectContaining({
            'X-Custom-Header': 'custom-value',
          }),
        })
      );
    });
  });
});
