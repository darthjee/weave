from django.contrib import admin
from .models.person import Person


@admin.register(Person)
class PersonAdmin(admin.ModelAdmin):
    list_display = ('first_name', 'middle_name', 'last_name', 'email', 'created_at')
    search_fields = ('first_name', 'middle_name', 'last_name', 'email')
    list_filter = ('created_at', 'updated_at')
    ordering = ('last_name', 'first_name')
