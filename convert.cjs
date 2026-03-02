const sharp = require('sharp');
const fs = require('fs');

async function convert(file, out) {
  try {
    await sharp(file).webp({ quality: 80 }).toFile(out);
    console.log(`Converted ${file} to ${out}`);
  } catch (err) {
    console.error(err);
  }
}

convert('assets/images/background/page_title.jpg', 'assets/images/background/page_title.webp');
convert('assets/images/background/featurelist_bg.jpg', 'assets/images/background/featurelist_bg.webp');
convert('assets/images/investigacion_play.png', 'assets/images/investigacion_play.webp');
