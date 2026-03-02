const fs = require('fs');
const path = require('path');

const blocksDir = path.join(__dirname, 'src', 'blocks');
const dirs = fs.readdirSync(blocksDir, { withFileTypes: true })
    .filter(dirent => dirent.isDirectory())
    .map(dirent => dirent.name);

dirs.forEach(dir => {
    const indexPath = path.join(blocksDir, dir, 'index.js');
    if (fs.existsSync(indexPath)) {
        let content = fs.readFileSync(indexPath, 'utf8');
        if (!content.includes("import './style.scss';")) {
            content = "import './style.scss';\n" + content;
            fs.writeFileSync(indexPath, content, 'utf8');
            console.log(`Updated ${dir}/index.js`);
        } else {
            console.log(`Skipped ${dir}/index.js (already has import)`);
        }
    }
});
