import { CurriculumClient } from '../../assets/js/clients/CurriculumClient.js';

describe('CurriculumClient', function() {
  let client;
  
  beforeEach(function() {
    client = new CurriculumClient();
  });

  describe('person()', function() {
    it('should fetch person data successfully', async function() {
      const mockPersonData = {
        id: 1,
        full_name: 'John Doe',
        first_name: 'John',
        last_name: 'Doe',
        email: 'john@example.com',
        years_of_experience: 5,
        roles: ['backend', 'frontend']
      };

      spyOn(global, 'fetch').and.returnValue(
        Promise.resolve({
          ok: true,
          json: () => Promise.resolve(mockPersonData)
        })
      );

      const result = await client.person();

      expect(global.fetch).toHaveBeenCalledWith('/api/curriculum/person/');
      expect(result).toEqual(mockPersonData);
    });

    it('should throw error when fetch fails', async function() {
      spyOn(global, 'fetch').and.returnValue(
        Promise.resolve({
          ok: false,
          status: 500
        })
      );

      try {
        await client.person();
        fail('Expected an error to be thrown');
      } catch (error) {
        expect(error.message).toBe('Failed to fetch person data');
      }

      expect(global.fetch).toHaveBeenCalledWith(
        `/api/curriculum/person/`
      );
    });

    it('should throw error when fetch rejects', async function() {
      const networkError = new Error('Network error');
      spyOn(global, 'fetch').and.returnValue(
        Promise.reject(networkError)
      );

      try {
        await client.person();
        fail('Expected an error to be thrown');
      } catch (error) {
        expect(error).toBe(networkError);
      }
    });

    it('should handle 404 not found', async function() {
      spyOn(global, 'fetch').and.returnValue(
        Promise.resolve({
          ok: false,
          status: 404
        })
      );

      try {
        await client.person();
        fail('Expected an error to be thrown');
      } catch (error) {
        expect(error.message).toBe('Failed to fetch person data');
      }
    });

    it('should parse JSON response correctly', async function() {
      const mockData = {
        full_name: 'Jane Smith',
        years_of_experience: 10
      };

      const mockJsonFn = jasmine.createSpy('json').and.returnValue(
        Promise.resolve(mockData)
      );

      spyOn(global, 'fetch').and.returnValue(
        Promise.resolve({
          ok: true,
          json: mockJsonFn
        })
      );

      const result = await client.person();

      expect(mockJsonFn).toHaveBeenCalled();
      expect(result).toEqual(mockData);
    });
  });
});
