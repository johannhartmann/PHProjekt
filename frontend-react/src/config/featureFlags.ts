/**
 * Feature Flags Configuration
 *
 * Controls which modules use React vs legacy Dojo implementation.
 * This enables gradual migration using the strangler pattern.
 */

export interface FeatureFlags {
  /**
   * Settings module migration
   */
  settings: {
    /** Enable React Settings module */
    enabled: boolean;
    /** Which setting modules to show in React */
    modules: {
      User: boolean;
      Notification: boolean;
      Calendar2: boolean;
      Timecard: boolean;
    };
  };

  /**
   * Project module migration (future)
   */
  projects: {
    enabled: boolean;
  };

  /**
   * Calendar module migration (future)
   */
  calendar: {
    enabled: boolean;
  };

  /**
   * Timecard module migration (future)
   */
  timecard: {
    enabled: boolean;
  };
}

/**
 * Default feature flags
 * In production, these could be loaded from backend API
 */
export const featureFlags: FeatureFlags = {
  settings: {
    enabled: true, // React Settings is live!
    modules: {
      User: true, // User settings migrated
      Notification: false, // Keep in Dojo for now
      Calendar2: false,
      Timecard: false,
    },
  },

  projects: {
    enabled: false, // Not migrated yet
  },

  calendar: {
    enabled: false, // Not migrated yet
  },

  timecard: {
    enabled: false, // Not migrated yet
  },
};

/**
 * Check if a feature is enabled
 */
export function isFeatureEnabled(feature: keyof FeatureFlags): boolean {
  return featureFlags[feature]?.enabled ?? false;
}

/**
 * Get feature flags (could be async in future to load from backend)
 */
export function getFeatureFlags(): FeatureFlags {
  return featureFlags;
}

/**
 * Update feature flags (for testing/development)
 */
export function setFeatureFlag(
  feature: keyof FeatureFlags,
  enabled: boolean
): void {
  if (featureFlags[feature]) {
    (featureFlags[feature] as any).enabled = enabled;
  }
}
