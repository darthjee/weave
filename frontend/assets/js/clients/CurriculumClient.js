// Support both Vite (browser) and Node.js (test) environments
const API_URL = import.meta.env?.VITE_WEAVE_API_URL || 'http://server:3000';

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
