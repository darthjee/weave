
from django.shortcuts import render
from django.urls import path
import time

def home(request):
    return render(request, "home.html", {
        'timestamp': int(time.time())
    })

urlpatterns = [
    path('', home),
]
