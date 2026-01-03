// Support both Vite (import.meta.env) and Node.js test environments
const getApiUrl = () => {
  // In Vite environment (browser)
  if (typeof import.meta !== 'undefined' && import.meta.env) {
    return import.meta.env.VITE_WEAVE_API_URL || 'http://localhost:3030';
  }
  // In test environment - tests will pass explicit URL to constructor
  return 'http://server:3000';
};

const API_URL = getApiUrl();

class CurriculumClient {
  constructor(baseUrl) {
    this.baseUrl = baseUrl;
  }

  async person() {
    const response = await fetch(`${this.baseUrl}/api/curriculum/person/`);
    if (!response.ok) {
      throw new Error('Failed to fetch person data');
    }
    return response.json();
  }
}

// Export default instance
export default new CurriculumClient(API_URL);

// Export class for testing or custom instances
export { CurriculumClient };
