const fs = require('fs');
const path = require('path');

const blocksDir = path.join(__dirname, 'src', 'blocks');
const dirs = fs.readdirSync(blocksDir, { withFileTypes: true })
    .filter(dirent => dirent.isDirectory())
    .map(dirent => dirent.name);

dirs.forEach(dir => {
    // 1. Fix block.json
    const jsonPath = path.join(blocksDir, dir, 'block.json');
    if (fs.existsSync(jsonPath)) {
        try {
            const blockData = JSON.parse(fs.readFileSync(jsonPath, 'utf8'));
            
            // Remove parent constraint (Decoupling)
            if (blockData.parent) {
                delete blockData.parent;
                console.log(`[block.json] Removed parent from ${dir}`);
            }
            
            // Add WPDS supports
            if (!blockData.supports) blockData.supports = {};
            
            if (!blockData.supports.color) {
                blockData.supports.color = { text: true, background: true, gradients: true };
            }
            if (!blockData.supports.typography) {
                blockData.supports.typography = {
                    fontSize: true,
                    lineHeight: true,
                    __experimentalFontFamily: true,
                    __experimentalFontWeight: true,
                    __experimentalDefaultControls: { fontSize: true }
                };
            }
            if (!blockData.supports.spacing) {
                blockData.supports.spacing = { margin: true, padding: true, blockGap: true };
            }
            
            fs.writeFileSync(jsonPath, JSON.stringify(blockData, null, 2), 'utf8');
        } catch (e) {
            console.error(`Error parsing ${jsonPath}:`, e);
        }
    }

    // 2. Fix InnerBlocks save in specific containers (Persistencia)
    if (['visual-timeline', 'program-modules', 'event-schedule'].includes(dir)) {
        const indexPath = path.join(blocksDir, dir, 'index.js');
        if (fs.existsSync(indexPath)) {
            let content = fs.readFileSync(indexPath, 'utf8');
            
            // Ensure InnerBlocks is imported if not present
            if (!content.includes('InnerBlocks') && content.includes('@wordpress/block-editor')) {
                content = content.replace(/import\s+{([^}]+)}\s+from\s+'@wordpress\/block-editor';/, (match, p1) => {
                    return `import { InnerBlocks, ${p1.trim()} } from '@wordpress/block-editor';`;
                });
            }
            
            // Replace save: () => null
            content = content.replace(/save:\s*\(\)\s*=>\s*null,?/g, "save: () => <InnerBlocks.Content />,");
            
            fs.writeFileSync(indexPath, content, 'utf8');
            console.log(`[index.js] Fixed InnerBlocks save persistence for ${dir}`);
        }
    }
});

console.log("Refactor script v2 for Sprint 1 completely executed!");
