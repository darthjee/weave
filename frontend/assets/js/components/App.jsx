import { useQuery } from '@tanstack/react-query';
import Header from './Header';

export default function App() {
  const { data, isLoading, error } = useQuery({
    queryKey: ['person'],
    queryFn: async () => {
      const response = await fetch('http://localhost:3030/api/curriculum/person/');
      if (!response.ok) {
        throw new Error('Failed to fetch person data');
      }
      return response.json();
    }
  });

  if (isLoading) {
    return (
      <div className="d-flex justify-content-center align-items-center" style={{ minHeight: '100vh' }}>
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="alert alert-danger m-3" role="alert">
        Error loading data: {error.message}
      </div>
    );
  }

  return (
    <Header 
      full_name={data.full_name}
      years_of_experience={data.years_of_experience}
      roles={data.roles}
    />
  );
}
