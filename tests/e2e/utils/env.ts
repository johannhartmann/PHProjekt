import dotenv from 'dotenv';
import path from 'path';

// Load environment variables
dotenv.config({ path: path.resolve(__dirname, '../.env.e2e') });

/**
 * Test environment configuration
 */
export const config = {
  baseURL: process.env.BASE_URL || 'http://localhost:8080',

  // Test user credentials
  testUser: {
    username: process.env.TEST_USER_USERNAME || 'test',
    password: process.env.TEST_USER_PASSWORD || 'test',
  },

  // Admin user credentials
  adminUser: {
    username: process.env.TEST_ADMIN_USERNAME || 'admin',
    password: process.env.TEST_ADMIN_PASSWORD || 'admin',
  },

  // Timeouts
  timeout: parseInt(process.env.TEST_TIMEOUT || '30000'),
  navigationTimeout: parseInt(process.env.NAVIGATION_TIMEOUT || '30000'),
};

export default config;
