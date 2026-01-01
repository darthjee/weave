from django.urls import path
from curriculum.views import PersonRetrieveView

urlpatterns = [
    path('person/<int:pk>/', PersonRetrieveView.as_view(), name='person-detail'),
]
