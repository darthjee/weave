import { useQuery } from '@tanstack/react-query';
import Header from './Header';
import CurriculumClient from '../clients/CurriculumClient';

export default function Curriculum() {
  const { data, isLoading, error } = useQuery({
    queryKey: ['person'],
    queryFn: () => CurriculumClient.person()
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
    <Header person={data} />
  );
}
