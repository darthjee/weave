import PropTypes from 'prop-types';

export default function SectionBox({ children }) {
  return (
    <div>
      {children}
    </div>
  );
}

SectionBox.propTypes = {
  children: PropTypes.node
};
