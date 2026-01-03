import SectionBox from './SectionBox';
import { yearsSince } from '../utils/yearUtils';

import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import Badge from 'react-bootstrap/Badge';

export default function Header({ person }) {
  const { full_name, first_experience, date_of_birth, roles = [], email } = person;
  const years_of_experience = yearsSince(first_experience);
  const age = yearsSince(date_of_birth);

  return (
    <Container fluid>
      <Row className="justify-content-md-center">
        <Col lg={8} md={10} sm={12}>
          <SectionBox>
            <Row className="border-bottom pb-2 mb-2">
              <Col md={6} className="text-center">
                <small className="text-muted d-block text-uppercase">Name</small>
                <strong className="fs-5">{full_name}</strong>
              </Col>
              <Col md={3} className="text-center">
                <small className="text-muted d-block text-uppercase">Email</small>
                <span className="d-block">{email || '—'}</span>
              </Col>
              <Col md={3} className="text-center">
                <small className="text-muted d-block text-uppercase">Age</small>
                <strong className="fs-5">{age}</strong>
              </Col>
            </Row>
            <Row>
              <Col md={9} className="text-center">
                <small className="text-muted d-block text-uppercase">Roles</small>
                <div className="mt-1">
                  {roles.length > 0 ? (
                    roles.map((role) => (
                      <Badge key={role} bg="primary" className="me-1 mb-1">
                        {role}
                      </Badge>
                    ))
                  ) : (
                    <span className="text-muted">—</span>
                  )}
                </div>
              </Col>
              <Col md={3} className="text-center">
                <small className="text-muted d-block text-uppercase">Years of Experience</small>
                <strong className="fs-5">{years_of_experience}</strong>
              </Col>
            </Row>
          </SectionBox>
        </Col>
      </Row>
    </Container>
  );
}
