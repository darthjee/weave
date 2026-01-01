from django.test import TestCase
from django.urls import reverse
from rest_framework import status
from rest_framework.test import APIClient
from curriculum.models.person import Person


class PersonRetrieveViewTest(TestCase):
    """
    Test cases for PersonRetrieveView endpoint.
    """
    
    def setUp(self):
        """Set up test client and sample data."""
        self.client = APIClient()
        self.person = Person.objects.create(
            first_name='John',
            middle_name='Michael',
            last_name='Doe',
            email='john.doe@example.com'
        )
        self.url = reverse('person-detail', kwargs={'pk': self.person.pk})
    
    def test_retrieve_person_success(self):
        """Test successfully retrieving a person by ID."""
        response = self.client.get(self.url)
        
        self.assertEqual(response.status_code, status.HTTP_200_OK)
        self.assertEqual(response.data['id'], self.person.id)
        self.assertEqual(response.data['first_name'], 'John')
        self.assertEqual(response.data['middle_name'], 'Michael')
        self.assertEqual(response.data['last_name'], 'Doe')
        self.assertEqual(response.data['email'], 'john.doe@example.com')
        self.assertEqual(response.data['full_name'], 'John Michael Doe')
    
    def test_retrieve_person_without_middle_name(self):
        """Test retrieving a person without a middle name."""
        person = Person.objects.create(
            first_name='Jane',
            last_name='Smith',
            email='jane.smith@example.com'
        )
        url = reverse('person-detail', kwargs={'pk': person.pk})
        
        response = self.client.get(url)
        
        self.assertEqual(response.status_code, status.HTTP_200_OK)
        self.assertEqual(response.data['first_name'], 'Jane')
        self.assertIsNone(response.data['middle_name'])
        self.assertEqual(response.data['last_name'], 'Smith')
        self.assertEqual(response.data['full_name'], 'Jane Smith')
    
    def test_retrieve_person_not_found(self):
        """Test retrieving a non-existent person returns 404."""
        url = reverse('person-detail', kwargs={'pk': 99999})
        response = self.client.get(url)
        
        self.assertEqual(response.status_code, status.HTTP_404_NOT_FOUND)
