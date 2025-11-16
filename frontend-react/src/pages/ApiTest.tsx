import { useState } from 'react';
import { api, ApiError, NetworkError } from '@/api';
import type {
  TimecardBooking,
  ProjectTreeNode,
  FrontendConfig,
} from '@/api';
import './ApiTest.css';

/**
 * API Test Page
 *
 * Demonstrates the API client functionality.
 * Tests various endpoints to verify integration.
 */
export function ApiTest() {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  // State for different API responses
  const [bookings, setBookings] = useState<TimecardBooking[]>([]);
  const [projectTree, setProjectTree] = useState<ProjectTreeNode[]>([]);
  const [config, setConfig] = useState<FrontendConfig | null>(null);

  /**
   * Handle API errors
   */
  const handleError = (err: unknown) => {
    if (err instanceof ApiError) {
      setError(`API Error: ${err.message} (Status: ${err.statusCode})`);
    } else if (err instanceof NetworkError) {
      setError(`Network Error: ${err.message}`);
    } else if (err instanceof Error) {
      setError(`Error: ${err.message}`);
    } else {
      setError('Unknown error occurred');
    }
  };

  /**
   * Test Timecard API
   */
  const testTimecardApi = async () => {
    setLoading(true);
    setError(null);

    try {
      const today = new Date().toISOString().split('T')[0];
      const data = await api.timecard.getDayBookings(today);
      setBookings(data);
    } catch (err) {
      handleError(err);
    } finally {
      setLoading(false);
    }
  };

  /**
   * Test Project Tree API
   */
  const testProjectTreeApi = async () => {
    setLoading(true);
    setError(null);

    try {
      const tree = await api.project.getProjectTree();
      setProjectTree(tree);
    } catch (err) {
      handleError(err);
    } finally {
      setLoading(false);
    }
  };

  /**
   * Test System Config API
   */
  const testSystemConfigApi = async () => {
    setLoading(true);
    setError(null);

    try {
      const cfg = await api.system.getConfig();
      setConfig(cfg);
    } catch (err) {
      handleError(err);
    } finally {
      setLoading(false);
    }
  };

  /**
   * Render project tree recursively
   */
  const renderProjectTree = (nodes: ProjectTreeNode[], depth = 0) => {
    return (
      <ul className="project-tree">
        {nodes.map((node) => (
          <li key={node.id} className="project-node" style={{ paddingLeft: `${depth * 20}px` }}>
            <strong>{node.title}</strong> <span className="project-id">(ID: {node.id})</span>
            {node.children && node.children.length > 0 && renderProjectTree(node.children, depth + 1)}
          </li>
        ))}
      </ul>
    );
  };

  return (
    <div className="api-test-page">
      <h1>API Client Test Page</h1>
      <p>Test the PHProjekt API client integration</p>

      {/* Loading indicator */}
      {loading && (
        <div className="loading-indicator">
          <div className="spinner"></div>
          <span>Loading...</span>
        </div>
      )}

      {/* Error display */}
      {error && (
        <div className="error-message">
          <strong>Error:</strong> {error}
        </div>
      )}

      {/* Test buttons */}
      <div className="test-buttons">
        <button onClick={testTimecardApi} disabled={loading}>
          Test Timecard API (Today's Bookings)
        </button>
        <button onClick={testProjectTreeApi} disabled={loading}>
          Test Project Tree API
        </button>
        <button onClick={testSystemConfigApi} disabled={loading}>
          Test System Config API
        </button>
      </div>

      {/* Results */}
      <div className="api-results">
        {/* Timecard Results */}
        {bookings.length > 0 && (
          <div className="result-section">
            <h2>Today's Timecard Bookings ({bookings.length})</h2>
            <table className="bookings-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Project</th>
                  <th>Start Time</th>
                  <th>End Time</th>
                  <th>Note</th>
                </tr>
              </thead>
              <tbody>
                {bookings.map((booking) => (
                  <tr key={booking.id}>
                    <td>{booking.id}</td>
                    <td>{booking.display}</td>
                    <td>{booking.startTime}</td>
                    <td>{booking.endTime || <em>Running</em>}</td>
                    <td>{booking.note}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        {/* Project Tree Results */}
        {projectTree.length > 0 && (
          <div className="result-section">
            <h2>Project Tree</h2>
            {renderProjectTree(projectTree)}
          </div>
        )}

        {/* System Config Results */}
        {config && (
          <div className="result-section">
            <h2>System Configuration</h2>
            <pre className="config-json">{JSON.stringify(config, null, 2)}</pre>
          </div>
        )}
      </div>

      {/* API Documentation */}
      <div className="api-documentation">
        <h2>Available API Modules</h2>
        <div className="api-modules">
          <div className="api-module">
            <h3>api.timecard</h3>
            <ul>
              <li>getDayBookings(date)</li>
              <li>getFavoriteProjects()</li>
              <li>getRunningBooking()</li>
              <li>saveBooking(booking)</li>
              <li>deleteBooking(id)</li>
            </ul>
          </div>

          <div className="api-module">
            <h3>api.project</h3>
            <ul>
              <li>getProjects(parentId, options)</li>
              <li>getProjectTree()</li>
              <li>getProject(id, nodeId)</li>
              <li>saveProject(project)</li>
              <li>deleteProject(id)</li>
              <li>getModulePermissions(projectId)</li>
              <li>getRoleUserRelations(projectId)</li>
            </ul>
          </div>

          <div className="api-module">
            <h3>api.tag</h3>
            <ul>
              <li>getAllTags()</li>
              <li>getTagsForItem(module, id)</li>
              <li>saveTags(module, id, tags)</li>
              <li>deleteTags(module, id)</li>
            </ul>
          </div>

          <div className="api-module">
            <h3>api.search</h3>
            <ul>
              <li>search(query)</li>
            </ul>
          </div>

          <div className="api-module">
            <h3>api.system</h3>
            <ul>
              <li>getConfig()</li>
              <li>getFrontendMessages()</li>
              <li>disableFrontendMessages()</li>
              <li>getTranslations(language)</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
}
