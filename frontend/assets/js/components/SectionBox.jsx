import PropTypes from 'prop-types';

export default function SectionBox({ children }) {
  return (
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
  );
}

SectionBox.propTypes = {
  children: PropTypes.node
};
