import PropTypes from 'prop-types';

import Container from 'react-bootstrap/Container';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';

export default function SectionBox({ children }) {
  return (
    <Container fluid>
      <Row>
        <Col lg={8} md={10} sm={12}>
          <div className="section-box">
          <div className="section-box-header">
            <div className="section-box-top-left-corner"></div>
            <div className="section-box-top-right-corner"></div>
          </div>
          <div className="section-box-middle">
            <div className="section-box-left-border"></div>
            <div className="section-box-content">
              {children}
            </div>
            <div className="section-box-right-border"></div>
          </div>
          <div className="section-box-footer">
            <div className="section-box-bottom-left-corner"></div>
            <div className="section-box-bottom-right-corner"></div>
          </div>
        </div>
        </Col>
      </Row>
    </Container>
  );
}

SectionBox.propTypes = {
  children: PropTypes.node
};
