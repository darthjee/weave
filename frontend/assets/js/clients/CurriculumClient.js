const API_URL = import.meta.env.VITE_WEAVE_API_URL || 'http://localhost:3030';

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
