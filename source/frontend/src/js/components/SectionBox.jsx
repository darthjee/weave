import PropTypes from 'prop-types';

export default function SectionBox({ children }) {
  return (
    <div className="section-box">
      <div className="section-box-header"></div>
      <div className="section-box-left-border"></div>
      <div className="section-box-content">
        {children}
      </div>
      <div className="section-box-right-border"></div>
      <div className="section-box-footer"></div>
    </div>
  );
}

SectionBox.propTypes = {
  children: PropTypes.node
};
