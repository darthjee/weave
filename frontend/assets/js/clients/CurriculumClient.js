class CurriculumClient {
  async person() {
    const response = await fetch('/api/curriculum/person/');
    if (!response.ok) {
      throw new Error('Failed to fetch person data');
    }
    return response.json();
  }
}

// Export default instance
export default new CurriculumClient();

// Export class for testing
export { CurriculumClient };
