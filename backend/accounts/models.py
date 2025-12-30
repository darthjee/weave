from django.contrib.auth.models import AbstractUser
from django.db import models


class User(AbstractUser):
    """
    Custom User model for Weave application.
    Extends Django's AbstractUser to allow future customizations.
    """
    
    # Add custom fields here as needed in the future
    # Example:
    # bio = models.TextField(max_length=500, blank=True)
    # avatar = models.ImageField(upload_to='avatars/', null=True, blank=True)
    
    class Meta:
        db_table = 'auth_user'  # Keep the same table name as Django's default
        verbose_name = 'user'
        verbose_name_plural = 'users'
    
    def __str__(self):
        return self.username
