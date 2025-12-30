import SectionBox from './SectionBox';

import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';

export default function Header() {
  return (
    <Container fluid>
      <Row className="justify-content-md-center">
        <Col lg={8} md={10} sm={12}>
          <SectionBox>
            <h1>Weave React App Header</h1>
            <p>This is a minimal React app served by Django static files.</p>
          </SectionBox>
        </Col>
      </Row>
    </Container>
  );
}
