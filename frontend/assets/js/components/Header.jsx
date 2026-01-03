import SectionBox from './SectionBox';
import { differenceInYears } from 'date-fns';

import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import Badge from 'react-bootstrap/Badge';

const calculateYearsOfExperience = (firstExperience) => {
  if (!firstExperience) return 0;
  return differenceInYears(new Date(), new Date(firstExperience));
};

export default function Header({ person }) {
  const { full_name, first_experience, roles = [] } = person;
  const years_of_experience = calculateYearsOfExperience(first_experience);

  return (
    <Container fluid>
      <Row className="justify-content-md-center">
        <Col lg={8} md={10} sm={12}>
          <SectionBox>
            <h1>{full_name}</h1>
            <p>
              {years_of_experience} {years_of_experience === 1 ? 'year' : 'years'} of experience
            </p>
            {roles.length > 0 && (
              <div className="mt-2">
                {roles.map((role) => (
                  <Badge key={role} bg="primary" className="me-2">
                    {role}
                  </Badge>
                ))}
              </div>
            )}
          </SectionBox>
        </Col>
      </Row>
    </Container>
  );
}
