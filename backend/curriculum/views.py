from rest_framework import generics
from rest_framework.response import Response
from rest_framework import status
from curriculum.models.person import Person
from curriculum.serializers import PersonSerializer


class PersonRetrieveView(generics.RetrieveAPIView):
    """
    API endpoint that retrieves a single person by ID.
    
    GET /api/curriculum/person/{id}/
    """
    queryset = Person.objects.all()
    serializer_class = PersonSerializer


class DefaultPersonView(generics.GenericAPIView):
    """
    API endpoint that retrieves the first person.
    
    GET /api/curriculum/person/
    """
    serializer_class = PersonSerializer
    
    def get(self, request, *args, **kwargs):
        person = Person.objects.first()
        if person is None:
            return Response(
                {"detail": "No person found."},
                status=status.HTTP_404_NOT_FOUND
            )
        serializer = self.get_serializer(person)
        return Response(serializer.data)
