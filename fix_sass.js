const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

function walkDir(dir, callback) {
  fs.readdirSync(dir).forEach(f => {
    let dirPath = path.join(dir, f);
    let isDirectory = fs.statSync(dirPath).isDirectory();
    isDirectory ? walkDir(dirPath, callback) : callback(path.join(dir, f));
  });
}

walkDir("C:\\Users\\UPIC\\Local Sites\\vpindev\\app\\public\\wp-content\\themes\\vpinunf\\src\\blocks", function(filePath) {
  if (filePath.endsWith('.scss')) {
    let data = fs.readFileSync(filePath, 'utf-8');
    if (data.includes('@import "../../scss/base/mixins";')) {
      let result = data.replace('@import "../../scss/base/mixins";', '@use "../../scss/base/mixins" as *;');
      fs.writeFileSync(filePath, result, 'utf8');
      console.log('Fixed:', filePath);
    }
  }
});
console.log('Done fixing scss files!');
