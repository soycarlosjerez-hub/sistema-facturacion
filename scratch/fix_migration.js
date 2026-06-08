import fs from 'fs';
import path from 'path';

function processFile(filePath) {
    let content = fs.readFileSync(filePath, 'utf8');
    
    // Fix double prefixes created by flawed regex
    content = content.replace(/d-d-flex/g, 'd-flex');
    content = content.replace(/d-d-block/g, 'd-block');
    content = content.replace(/d-d-none/g, 'd-none');
    content = content.replace(/d-d-inline/g, 'd-inline');
    content = content.replace(/align-align-items/g, 'align-items');
    content = content.replace(/justify-content-content/g, 'justify-content');
    content = content.replace(/d-flex-column/g, 'flex-column');
    content = content.replace(/d-flex-row/g, 'flex-row');
    content = content.replace(/d-flex-wrap/g, 'flex-wrap');
    content = content.replace(/d-flex-grow/g, 'flex-grow');
    content = content.replace(/d-flex-shrink/g, 'flex-shrink');
    content = content.replace(/overflow-d-none/g, 'overflow-hidden');
    content = content.replace(/min-h-screen/g, 'vh-100');

    fs.writeFileSync(filePath, content, 'utf8');
}

function processDirectory(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        const stat = fs.statSync(fullPath);
        if (stat.isDirectory()) {
            processDirectory(fullPath);
        } else if (file.endsWith('.blade.php') || file.endsWith('.css') || file.endsWith('.scss')) {
            processFile(fullPath);
        }
    }
}

const viewsDir = path.resolve('resources');
console.log('Fixing double prefixes in:', viewsDir);
processDirectory(viewsDir);
console.log('Fix completed.');
