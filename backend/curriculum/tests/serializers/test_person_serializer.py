from django.test import TestCase
from curriculum.models.person import Person
from curriculum.serializers import PersonSerializer


class PersonSerializerTest(TestCase):
    """
    Test cases for PersonSerializer.
    """
    
    def test_serialize_person_with_all_fields(self):
        """Test serializing a person with all fields including middle name."""
        person = Person.objects.create(
            first_name='John',
            middle_name='Michael',
            last_name='Doe',
            email='john.doe@example.com'
        )
        
        serializer = PersonSerializer(person)
        data = serializer.data
        
        self.assertEqual(data['id'], person.id)
        self.assertEqual(data['first_name'], 'John')
        self.assertEqual(data['middle_name'], 'Michael')
        self.assertEqual(data['last_name'], 'Doe')
        self.assertEqual(data['email'], 'john.doe@example.com')
        self.assertEqual(data['full_name'], 'John Michael Doe')
        self.assertIn('created_at', data)
        self.assertIn('updated_at', data)
    
    def test_serialize_person_without_middle_name(self):
        """Test serializing a person without middle name."""
        person = Person.objects.create(
            first_name='Jane',
            last_name='Smith',
            email='jane.smith@example.com'
        )
        
        serializer = PersonSerializer(person)
        data = serializer.data
        
        self.assertEqual(data['first_name'], 'Jane')
        self.assertIsNone(data['middle_name'])
        self.assertEqual(data['last_name'], 'Smith')
        self.assertEqual(data['full_name'], 'Jane Smith')
    
    def test_deserialize_valid_data(self):
        """Test deserializing valid data to create a person."""
        data = {
            'first_name': 'Alice',
            'middle_name': 'Marie',
            'last_name': 'Johnson',
            'email': 'alice.johnson@example.com'
        }
        
        serializer = PersonSerializer(data=data)
        
        self.assertTrue(serializer.is_valid())
        person = serializer.save()
        
        self.assertEqual(person.first_name, 'Alice')
        self.assertEqual(person.middle_name, 'Marie')
        self.assertEqual(person.last_name, 'Johnson')
        self.assertEqual(person.email, 'alice.johnson@example.com')
    
    def test_deserialize_without_middle_name(self):
        """Test deserializing data without middle name (optional field)."""
        data = {
            'first_name': 'Bob',
            'last_name': 'Brown',
            'email': 'bob.brown@example.com'
        }
        
        serializer = PersonSerializer(data=data)
        
        self.assertTrue(serializer.is_valid())
        person = serializer.save()
        
        self.assertEqual(person.first_name, 'Bob')
        self.assertIsNone(person.middle_name)
        self.assertEqual(person.last_name, 'Brown')
    
    def test_deserialize_missing_required_fields(self):
        """Test that missing required fields cause validation errors."""
        data = {
            'first_name': 'Charlie'
            # Missing last_name and email
        }
        
        serializer = PersonSerializer(data=data)
        
        self.assertFalse(serializer.is_valid())
        self.assertIn('last_name', serializer.errors)
        self.assertIn('email', serializer.errors)
    
    def test_deserialize_invalid_email(self):
        """Test that invalid email format causes validation error."""
        data = {
            'first_name': 'David',
            'last_name': 'Davis',
            'email': 'invalid-email'
        }
        
        serializer = PersonSerializer(data=data)
        
        self.assertFalse(serializer.is_valid())
        self.assertIn('email', serializer.errors)
    
    def test_update_person(self):
        """Test updating an existing person."""
        person = Person.objects.create(
            first_name='Eve',
            last_name='Evans',
            email='eve.evans@example.com'
        )
        
        update_data = {
            'first_name': 'Evelyn',
            'middle_name': 'Rose',
            'last_name': 'Evans',
            'email': 'evelyn.evans@example.com'
        }
        
        serializer = PersonSerializer(person, data=update_data)
        
        self.assertTrue(serializer.is_valid())
        updated_person = serializer.save()
        
        self.assertEqual(updated_person.id, person.id)
        self.assertEqual(updated_person.first_name, 'Evelyn')
        self.assertEqual(updated_person.middle_name, 'Rose')
        self.assertEqual(updated_person.email, 'evelyn.evans@example.com')
    
    def test_full_name_is_read_only(self):
        """Test that full_name field is read-only and cannot be set directly."""
        data = {
            'first_name': 'Frank',
            'last_name': 'Foster',
            'email': 'frank.foster@example.com',
            'full_name': 'This Should Be Ignored'
        }
        
        serializer = PersonSerializer(data=data)
        
        self.assertTrue(serializer.is_valid())
        person = serializer.save()
        
        # full_name should be computed from the model, not from input
        self.assertEqual(person.full_name, 'Frank Foster')
        self.assertNotEqual(person.full_name, 'This Should Be Ignored')
    
    def test_read_only_fields_cannot_be_updated(self):
        """Test that id, created_at, updated_at are read-only."""
        person = Person.objects.create(
            first_name='Grace',
            last_name='Green',
            email='grace.green@example.com'
        )
        
        original_id = person.id
        original_created_at = person.created_at
        
        update_data = {
            'id': 99999,  # Should be ignored
            'first_name': 'Grace',
            'last_name': 'Green',
            'email': 'grace.green@example.com',
            'created_at': '2020-01-01T00:00:00Z'  # Should be ignored
        }
        
        serializer = PersonSerializer(person, data=update_data)
        
        self.assertTrue(serializer.is_valid())
        updated_person = serializer.save()
        
        # ID and created_at should not change
        self.assertEqual(updated_person.id, original_id)
        self.assertEqual(updated_person.created_at, original_created_at)
    
    def test_serialize_multiple_people(self):
        """Test serializing a queryset of multiple people."""
        Person.objects.create(
            first_name='Alice',
            last_name='Anderson',
            email='alice@example.com'
        )
        Person.objects.create(
            first_name='Bob',
            last_name='Brown',
            email='bob@example.com'
        )
        
        people = Person.objects.all()
        serializer = PersonSerializer(people, many=True)
        
        self.assertEqual(len(serializer.data), 2)
        self.assertEqual(serializer.data[0]['first_name'], 'Alice')
        self.assertEqual(serializer.data[1]['first_name'], 'Bob')
