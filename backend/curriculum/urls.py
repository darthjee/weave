from django.urls import path
from curriculum.views import PersonRetrieveView, FirstPersonView

urlpatterns = [
    path('person/', FirstPersonView.as_view(), name='first-person'),
    path('person/<int:pk>/', PersonRetrieveView.as_view(), name='person-detail'),
]
