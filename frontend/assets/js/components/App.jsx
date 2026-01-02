import Header from './Header';

export default function App() {
  // Mocked data
  const personData = {
    full_name: "Developer Name",
    years_of_experience: Math.floor(Math.random() * 50) + 1,
    roles: ['backend', 'frontend']
  };

  return (
    <Header 
      full_name={personData.full_name}
      years_of_experience={personData.years_of_experience}
      roles={personData.roles}
    />
  );
}
