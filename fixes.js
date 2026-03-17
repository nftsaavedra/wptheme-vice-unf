import fs from 'fs';
const files = [
  'src/blocks/program-modules/index.js',
  'src/blocks/module-card/index.js',
  'src/blocks/icon-categories/index.js',
  'src/blocks/footer-cta/index.js',
  'src/blocks/benefit-card/index.js',
  'src/blocks/visual-timeline/index.js',
  'src/blocks/schedule-card/index.js',
  'src/blocks/timeline-step/index.js'
];

files.forEach(f => {
  let s = fs.readFileSync(f, 'utf8');
  s = s.replace(/ColorPicker/g, 'ColorPalette');
  s = s.replace(/color=\{\s*([a-zA-Z0-9_]+)\s*\}/g, 'value={ $1 }');
  s = s.replace(/([\t ]+)enableAlpha=\{\s*false\s*\}/g, '');
  fs.writeFileSync(f, s);
  console.log(f + ' fixed');
});
