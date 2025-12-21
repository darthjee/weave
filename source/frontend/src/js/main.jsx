import { createRoot } from 'react-dom/client';
import App from './components/App';

import '../css/styles.css';
import '../css/main.css';

(function () {
  createRoot(document.getElementById('root')).render(
    <App />
  );
})();
