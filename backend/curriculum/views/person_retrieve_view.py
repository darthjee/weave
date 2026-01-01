from rest_framework import generics
from rest_framework.renderers import JSONRenderer
from curriculum.models.person import Person
from curriculum.serializers import PersonSerializer


class PersonRetrieveView(generics.RetrieveAPIView):
    """
    API endpoint that retrieves a single person by ID.
    
    GET /api/curriculum/person/{id}/
    """
    queryset = Person.objects.all()
    serializer_class = PersonSerializer
    renderer_classes = [JSONRenderer]
