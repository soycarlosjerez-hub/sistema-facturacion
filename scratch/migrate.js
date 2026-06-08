import fs from 'fs';
import path from 'path';

const classMap = {
    // Flexbox & Grid
    'flex': 'd-flex',
    'flex-col': 'flex-column',
    'flex-row': 'flex-row',
    'flex-wrap': 'flex-wrap',
    'items-center': 'align-items-center',
    'items-start': 'align-items-start',
    'items-end': 'align-items-end',
    'justify-center': 'justify-content-center',
    'justify-between': 'justify-content-between',
    'justify-end': 'justify-content-end',
    'justify-start': 'justify-content-start',
    'hidden': 'd-none',
    'block': 'd-block',
    'inline-block': 'd-inline-block',
    // Sizing
    'w-full': 'w-100',
    'w-1/2': 'w-50',
    'w-1/4': 'w-25',
    'h-full': 'h-100',
    'max-w-7xl': 'container',
    'max-w-md': 'container-sm',
    'max-w-lg': 'container-sm',
    'max-w-xl': 'container-md',
    // Typography
    'text-center': 'text-center',
    'text-left': 'text-start',
    'text-right': 'text-end',
    'font-bold': 'fw-bold',
    'font-semibold': 'fw-semibold',
    'font-light': 'fw-light',
    'text-sm': 'fs-6',
    'text-base': 'fs-6',
    'text-lg': 'fs-5',
    'text-xl': 'fs-4',
    'text-2xl': 'fs-3',
    'text-3xl': 'fs-2',
    'text-4xl': 'fs-1',
    // Colors
    'text-white': 'text-white',
    'text-black': 'text-dark',
    'text-gray-500': 'text-secondary',
    'text-gray-600': 'text-secondary',
    'text-gray-700': 'text-dark',
    'text-gray-800': 'text-dark',
    'text-gray-900': 'text-dark',
    'text-red-500': 'text-danger',
    'text-red-600': 'text-danger',
    'text-green-500': 'text-success',
    'text-green-600': 'text-success',
    'text-blue-500': 'text-primary',
    'text-blue-600': 'text-primary',
    'text-yellow-500': 'text-warning',
    'text-yellow-600': 'text-warning',
    'bg-white': 'bg-white',
    'bg-black': 'bg-dark',
    'bg-gray-100': 'bg-light',
    'bg-gray-200': 'bg-light',
    'bg-gray-800': 'bg-dark',
    'bg-gray-900': 'bg-dark',
    'bg-blue-500': 'bg-primary',
    'bg-blue-600': 'bg-primary',
    'bg-red-500': 'bg-danger',
    'bg-red-600': 'bg-danger',
    'bg-green-500': 'bg-success',
    'bg-green-600': 'bg-success',
    'bg-yellow-500': 'bg-warning',
    'bg-yellow-600': 'bg-warning',
    // Borders & Shadows
    'rounded': 'rounded',
    'rounded-md': 'rounded-2',
    'rounded-lg': 'rounded-3',
    'rounded-full': 'rounded-circle',
    'shadow': 'shadow',
    'shadow-sm': 'shadow-sm',
    'shadow-md': 'shadow',
    'shadow-lg': 'shadow-lg',
    'border': 'border',
    'border-gray-200': 'border-light',
    'border-gray-300': 'border-secondary',
    // States
    'hover:bg-blue-700': 'btn-primary',
    'hover:bg-gray-100': 'bg-light',
};

const mapRegex = new RegExp(`\\b(${Object.keys(classMap).join('|')})\\b`, 'g');

// Complex regexes
const complexRules = [
    // Margins and Paddings (Tailwind uses 0.25rem increments, Bootstrap uses similar scale 0-5)
    // T: p-4 = 1rem, B: p-3 = 1rem. T: p-2 = 0.5rem, B: p-2 = 0.5rem. 
    // We'll just map 1:1 up to 5, and anything above 5 map to 5 to avoid invalid bootstrap classes.
    {
        regex: /\b([pm][xytrbl]?)-(\d+)\b/g,
        replace: (match, prefix, num) => {
            let n = parseInt(num);
            if (n > 5) n = 5;
            return `${prefix}-${n}`;
        }
    },
    // Grid conversions
    {
        regex: /\bgrid grid-cols-(\d+)\b/g,
        replace: (match, num) => {
            return `row row-cols-${num}`;
        }
    },
    {
        regex: /\bcol-span-(\d+)\b/g,
        replace: (match, num) => {
            return `col-${num}`;
        }
    },
    {
        regex: /\bgap-(\d+)\b/g,
        replace: (match, num) => {
            let n = parseInt(num);
            if (n > 5) n = 5;
            return `gap-${n}`;
        }
    },
    // Responsive variants
    {
        regex: /\b(sm|md|lg|xl):([a-z0-9-]+)\b/g,
        replace: (match, bp, cls) => {
            // Very simplified: try to adapt flex, text, block, hidden
            if (cls === 'flex') return `d-${bp}-flex`;
            if (cls === 'hidden') return `d-${bp}-none`;
            if (cls === 'block') return `d-${bp}-block`;
            // Map text centers
            if (cls === 'text-center') return `text-${bp}-center`;
            if (cls === 'text-left') return `text-${bp}-start`;
            if (cls === 'text-right') return `text-${bp}-end`;
            // Map common widths (rough)
            if (cls.startsWith('w-')) return `w-${bp}-100`; // Bootstrap only supports w-100 really, not breakpoints natively unless custom, fallback to col if grid.
            return ''; // Remove other responsive tailwind if we can't translate easily
        }
    }
];

function processFile(filePath) {
    let content = fs.readFileSync(filePath, 'utf8');
    
    // Process exact map
    content = content.replace(mapRegex, match => classMap[match]);
    
    // Process complex rules
    complexRules.forEach(rule => {
        content = content.replace(rule.regex, rule.replace);
    });

    // Write back
    fs.writeFileSync(filePath, content, 'utf8');
}

function processDirectory(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        const stat = fs.statSync(fullPath);
        if (stat.isDirectory()) {
            processDirectory(fullPath);
        } else if (file.endsWith('.blade.php')) {
            processFile(fullPath);
        }
    }
}

const viewsDir = path.resolve('resources/views');
console.log('Migrating Tailwind to Bootstrap in:', viewsDir);
processDirectory(viewsDir);
console.log('Migration completed.');
