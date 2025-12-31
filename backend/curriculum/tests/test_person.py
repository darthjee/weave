from django.test import TestCase
from curriculum.models import Person


class PersonModelTest(TestCase):
    """
    Test cases for Person model.
    """
    
    def test_full_name_with_middle_name(self):
        """Test full_name property with middle name."""
        person = Person(
            first_name="João",
            middle_name="Pedro",
            last_name="Silva"
        )
        self.assertEqual(person.full_name, "João Pedro Silva")
    
    def test_full_name_without_middle_name(self):
        """Test full_name property without middle name."""
        person = Person(
            first_name="Maria",
            last_name="Santos"
        )
        self.assertEqual(person.full_name, "Maria Santos")
    
    def test_full_name_with_empty_middle_name(self):
        """Test full_name property with empty string as middle name."""
        person = Person(
            first_name="Carlos",
            middle_name="",
            last_name="Oliveira"
        )
        self.assertEqual(person.full_name, "Carlos Oliveira")
    
    def test_str_representation(self):
        """Test __str__ method."""
        person = Person(
            first_name="Ana",
            last_name="Costa"
        )
        self.assertEqual(str(person), "Ana Costa")
