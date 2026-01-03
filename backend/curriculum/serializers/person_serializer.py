from rest_framework import serializers
from curriculum.models.person import Person


class PersonSerializer(serializers.ModelSerializer):
    """
    Serializer for Person model.
    """
    full_name = serializers.ReadOnlyField()
    
    class Meta:
        model = Person
        fields = [
            'id',
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'date_of_birth',
            'first_experience',
            'full_name',
            'created_at',
            'updated_at',
        ]
        read_only_fields = ['id', 'created_at', 'updated_at']
