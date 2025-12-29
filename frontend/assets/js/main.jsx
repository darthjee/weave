import { createRoot } from 'react-dom/client';
import App from './components/App';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import '../css/styles.css';
import '../css/main.scss';

(function () {
  createRoot(document.getElementById('root')).render(
    <App />
  );
})();
