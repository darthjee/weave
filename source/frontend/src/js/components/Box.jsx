import PropTypes from 'prop-types';

export default function Box({ children }) {
  return (
    <div>
      {children}
    </div>
  );
}

Box.propTypes = {
  children: PropTypes.node
};
