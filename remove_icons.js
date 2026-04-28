const fs = require('fs');
const path = require('path');

function walk(dir) {
    let res = [];
    let list = fs.readdirSync(dir);
    list.forEach(f => {
        let file = path.join(dir, f);
        if (fs.statSync(file).isDirectory()) {
            res = res.concat(walk(file));
        } else if (file.endsWith('.blade.php')) {
            res.push(file);
        }
    });
    return res;
}

let files = walk('./resources/views');
let removedCount = 0;

files.forEach(f => {
    let oldContent = fs.readFileSync(f, 'utf8');
    // Remove fontawesome icons
    let newContent = oldContent.replace(/<i\s+class="[^"]*\bfa[a-z0-9-]*\b[^"]*"[^>]*><\/i>\s*/gi, '');
    newContent = newContent.replace(/<i\s+class='[^']*\bfa[a-z0-9-]*\b[^']*'[^>]*><\/i>\s*/gi, '');
    
    // Remove generic fas and far prefixes followed by space and fa-
    newContent = newContent.replace(/<i\s+class="[^"]*\bfas\b[^"]*"[^>]*><\/i>\s*/gi, '');
    newContent = newContent.replace(/<i\s+class="[^"]*\bfar\b[^"]*"[^>]*><\/i>\s*/gi, '');
    
    // Fallback: remove anything `<i class="fa...` or `<i class="fas...` completely
    newContent = newContent.replace(/<i\s+class="[^"]*fa[^"]*"[^>]*>.*?<\/i>\s*/gi, '');

    // Remove raw SVG icons (keeping the layout logos since it has `<x-application-logo />` usually, but we stripped them manually earlier)
    // Actually, I'll strip all inline SVGs since the user hates icons
    newContent = newContent.replace(/<svg[\s\S]*?<\/svg>\s*/gi, '');

    if (newContent !== oldContent) {
        fs.writeFileSync(f, newContent);
        removedCount++;
    }
});

console.log(`Icons removed from ${removedCount} files.`);
