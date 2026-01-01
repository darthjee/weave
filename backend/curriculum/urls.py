from django.urls import path
from curriculum.views import PersonRetrieveView, DefaultPersonView

urlpatterns = [
    path('person/', DefaultPersonView.as_view(), name='first-person'),
    path('person/<int:pk>/', PersonRetrieveView.as_view(), name='person-detail'),
]
