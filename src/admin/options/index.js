import { createRoot } from '@wordpress/element';
import App from './App.js';

import './style.css'; // Optional styling for the admin page

document.addEventListener('DOMContentLoaded', () => {
    const rootElement = document.getElementById('viceunf-settings-root');
    if (rootElement) {
        // Use createRoot from @wordpress/element (which maps to React 18)
        const root = createRoot(rootElement);
        root.render(<App />);
    }
});
