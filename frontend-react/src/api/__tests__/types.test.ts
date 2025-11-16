/**
 * API Types Unit Tests
 *
 * Tests for TypeScript type guards and type utilities
 */

import { describe, it, expect } from 'vitest';
import { isSuccessResponse, isErrorResponse } from '../types';
import type { ApiResponse } from '../types';

describe('API Types', () => {
  describe('Type Guards', () => {
    describe('isSuccessResponse', () => {
      it('should return true for success responses', () => {
        const response: ApiResponse = {
          type: 'success',
          message: 'Operation successful',
          id: 123,
        };

        expect(isSuccessResponse(response)).toBe(true);
      });

      it('should return false for error responses', () => {
        const response: ApiResponse = {
          type: 'error',
          message: 'Operation failed',
        };

        expect(isSuccessResponse(response)).toBe(false);
      });

      it('should narrow type in conditional', () => {
        const response: ApiResponse<{ value: string }> = {
          type: 'success',
          data: { value: 'test' },
        };

        if (isSuccessResponse(response)) {
          // TypeScript should know response.data exists here
          expect(response.data?.value).toBe('test');
        }
      });
    });

    describe('isErrorResponse', () => {
      it('should return true for error responses', () => {
        const response: ApiResponse = {
          type: 'error',
          message: 'Something went wrong',
          code: 'VALIDATION_ERROR',
        };

        expect(isErrorResponse(response)).toBe(true);
      });

      it('should return false for success responses', () => {
        const response: ApiResponse = {
          type: 'success',
          message: 'OK',
        };

        expect(isErrorResponse(response)).toBe(false);
      });

      it('should narrow type in conditional', () => {
        const response: ApiResponse = {
          type: 'error',
          message: 'Error occurred',
          code: 'NOT_FOUND',
        };

        if (isErrorResponse(response)) {
          // TypeScript should know response.code may exist here
          expect(response.code).toBe('NOT_FOUND');
        }
      });
    });
  });

  describe('Type Compatibility', () => {
    it('should accept valid success response', () => {
      const response: ApiResponse<number[]> = {
        type: 'success',
        data: [1, 2, 3],
        message: 'Success',
      };

      expect(response.type).toBe('success');
      expect(response.data).toEqual([1, 2, 3]);
    });

    it('should accept success response without data', () => {
      const response: ApiResponse = {
        type: 'success',
        message: 'Deleted',
        id: 5,
      };

      expect(response.type).toBe('success');
      expect(response.data).toBeUndefined();
    });

    it('should accept error response with code', () => {
      const response: ApiResponse = {
        type: 'error',
        message: 'Invalid input',
        code: 'VALIDATION_FAILED',
      };

      expect(response.type).toBe('error');
      expect(response.code).toBe('VALIDATION_FAILED');
    });
  });
});
