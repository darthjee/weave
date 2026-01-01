from django.test import TestCase
from django.urls import reverse
from rest_framework import status
from rest_framework.test import APIClient
from curriculum.models.person import Person


class FirstPersonViewTest(TestCase):
    """
    Test cases for FirstPersonView endpoint.
    """
    
    def setUp(self):
        """Set up test client and sample data."""
        self.client = APIClient()
        self.url = reverse('first-person')
    
    def test_retrieve_first_person_success(self):
        """Test successfully retrieving the first person."""
        # Create multiple people
        person1 = Person.objects.create(
            first_name='Alice',
            last_name='Anderson',
            email='alice@example.com'
        )
        Person.objects.create(
            first_name='Bob',
            last_name='Brown',
            email='bob@example.com'
        )
        Person.objects.create(
            first_name='Charlie',
            last_name='Clark',
            email='charlie@example.com'
        )
        
        response = self.client.get(self.url)
        
        self.assertEqual(response.status_code, status.HTTP_200_OK)
        self.assertEqual(response.data['id'], person1.id)
        self.assertEqual(response.data['first_name'], 'Alice')
        self.assertEqual(response.data['last_name'], 'Anderson')
        self.assertEqual(response.data['email'], 'alice@example.com')
    
    def test_retrieve_first_person_with_middle_name(self):
        """Test retrieving the first person with a middle name."""
        person = Person.objects.create(
            first_name='John',
            middle_name='Michael',
            last_name='Doe',
            email='john.doe@example.com'
        )
        
        response = self.client.get(self.url)
        
        self.assertEqual(response.status_code, status.HTTP_200_OK)
        self.assertEqual(response.data['first_name'], 'John')
        self.assertEqual(response.data['middle_name'], 'Michael')
        self.assertEqual(response.data['last_name'], 'Doe')
        self.assertEqual(response.data['full_name'], 'John Michael Doe')
    
    def test_retrieve_first_person_no_people_exist(self):
        """Test retrieving when no people exist returns 404."""
        response = self.client.get(self.url)
        
        self.assertEqual(response.status_code, status.HTTP_404_NOT_FOUND)
        self.assertIn('detail', response.data)
        self.assertEqual(response.data['detail'], 'No person found.')
    
    def test_retrieve_first_person_includes_timestamps(self):
        """Test that timestamps are included in the response."""
        Person.objects.create(
            first_name='Jane',
            last_name='Smith',
            email='jane.smith@example.com'
        )
        
        response = self.client.get(self.url)
        
        self.assertEqual(response.status_code, status.HTTP_200_OK)
        self.assertIn('created_at', response.data)
        self.assertIn('updated_at', response.data)
        self.assertIsNotNone(response.data['created_at'])
        self.assertIsNotNone(response.data['updated_at'])
    
    def test_retrieve_first_person_ordered_by_model_ordering(self):
        """Test that the first person respects model ordering (last_name, first_name)."""
        # Create people in different order
        Person.objects.create(
            first_name='Zoe',
            last_name='Wilson',
            email='zoe@example.com'
        )
        person_first = Person.objects.create(
            first_name='Alice',
            last_name='Anderson',
            email='alice@example.com'
        )
        Person.objects.create(
            first_name='Bob',
            last_name='Brown',
            email='bob@example.com'
        )
        
        response = self.client.get(self.url)
        
        # Should return Alice Anderson (first by ordering)
        self.assertEqual(response.status_code, status.HTTP_200_OK)
        self.assertEqual(response.data['id'], person_first.id)
        self.assertEqual(response.data['first_name'], 'Alice')
        self.assertEqual(response.data['last_name'], 'Anderson')
