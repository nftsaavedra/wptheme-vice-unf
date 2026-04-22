const fs = require('fs');
const path = require('path');
const blocks = ['slider', 'investigation', 'about', 'event', 'production', 'post', 'partner'];

blocks.forEach(b => {
    const dir = path.join(process.cwd(), 'src', 'blocks', 'legacy-home-' + b);
    const content = `<?php
if(!defined('ABSPATH')){exit;}
if(!isset($attributes)){ $attributes = []; }
get_template_part('template-parts/site', '${b}', $attributes);
`;
    fs.writeFileSync(path.join(dir, 'render.php'), content, 'utf8');
});
console.log('Fixed render files');
