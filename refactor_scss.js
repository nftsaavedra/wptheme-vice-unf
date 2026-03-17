import fs from 'fs';
import path from 'path';

const blocksDir = 'src/blocks';
const filesToProcess = [
  'about-section/style.scss',
  'benefit-card/style.scss',
  'benefits-grid/style.scss',
  'carousel-entities/style.scss',
  'document-list/style.scss',
  'event-schedule/style.scss',
  'footer-cta/style.scss',
  'hero-lp/style.scss',
  'icon-categories/style.scss',
  'logo-partners/style.scss',
  'module-card/style.scss',
  'program-modules/style.scss',
  'recent-posts/style.scss',
  'schedule-card/style.scss',
  'timeline-step/style.scss',
  'visual-timeline/style.scss'
];

filesToProcess.forEach(file => {
  const fullPath = path.join(blocksDir, file);
  if (!fs.existsSync(fullPath)) return;
  
  let content = fs.readFileSync(fullPath, 'utf8');
  let originalContent = content;

  // 1. Remove .alignfull block
  content = content.replace(/\s*&\.alignfull\s*\{\s*width:\s*100vw;\s*margin-left:\s*calc\(50%\s*-\s*50vw\);\s*\}/g, '');

  // 2. Replace rigid media queries with mixins
  content = content.replace(/@media\s*\(\s*max-width:\s*61\.95em\s*\)/g, '@include mq(md)');
  content = content.replace(/@media\s*\(\s*max-width:\s*35\.95em\s*\)/g, '@include mq(sm)');
  content = content.replace(/@media\s*\(\s*max-width:\s*47\.95em\s*\)/g, '@include mq(sm)');
  content = content.replace(/@media\s*\(\s*max-width:\s*575px\s*\)/g, '@include mq(sm)');
  content = content.replace(/@media\s*\(\s*min-width:\s*62em\s*\)/g, '@media (min-width: 992px)'); // Not a mixin but standardization
  
  // Specific block fixes inside the iteration
  if (file === 'footer-cta/style.scss') {
    content = content.replace(/padding:\s*9rem\s*0;/, 'padding: clamp(3rem, 8vw, 9rem) 0;');
  }
  
  if (file === 'carousel-entities/style.scss') {
    content = content.replace(/height:\s*85px;/, 'min-height: 85px;');
  }
  
  if (file === 'hero-lp/style.scss') {
    // Buttons in hero-lp to be 100% and tap safe
    // The user requested: Modifica los botones para que en móvil tengan width: 100% y cumplan con el tamaño táctil mínimo (aplica el mixin tap-safe-target)
    // Looking at hero-lp/style.scss line 93:
    // .dt-btn { min-width: 18rem; @media (max-width: 35.95em) { width: 100%; text-align: center; } }
    // It's already there, just need to add the mixin
    content = content.replace(/@include mq\(sm\)\s*\{/, '@include mq(sm) {\n\t\t\t@include tap-safe-target;');
  }

  if (file === 'benefits-grid/style.scss') {
    // 1 column at sm (already has a media query for 35.95em but user asked to drop to 1 at sm)
    // "Ajusta la caída a 1 columna en el breakpoint sm (767px) usando @include mq(sm)"
    // It already has that drop, but let's make sure it's 1fr in the mq(sm) block. The regex replacement above already handles it.
  }

  if (file === 'benefit-card/style.scss') {
    content = content.replace(/padding:\s*3\.2rem\s*2\.4rem;/, 'padding: 3.2rem 2.4rem;\n\t@include mq(sm) {\n\t\tpadding: 1.5rem;\n\t}');
    content = content.replace(/&:hover\s*\{\s*transform:\s*translateY\(-6px\);\s*box-shadow:\s*0\s*2rem\s*4rem\s*rgba\(0,\s*0,\s*0,\s*0\.25\);\s*\}/g, 
        '@media (hover: hover) and (prefers-reduced-motion: no-preference) {\n\t\t&:hover {\n\t\t\ttransform: translateY(-6px);\n\t\t\tbox-shadow: 0 2rem 4rem rgba(0, 0, 0, 0.25);\n\t\t}\n\t}');
  }

  if (content !== originalContent) {
    fs.writeFileSync(fullPath, content);
    console.log(`Updated ${file}`);
  }
});
