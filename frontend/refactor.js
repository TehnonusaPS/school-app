const fs = require('fs');
const path = require('path');

const file = path.join(__dirname, 'src/modules/keuangan/pages/Spp.vue');
let content = fs.readFileSync(file, 'utf-8');

const replacements = {
  // Texts
  'text-slate-900': 'text-foreground',
  'text-slate-800': 'text-foreground',
  'text-slate-700': 'text-card-foreground',
  'text-slate-600': 'text-muted-foreground',
  'text-slate-500': 'text-muted-foreground',
  'text-slate-400': 'text-muted-foreground',
  'text-slate-300': 'text-muted-foreground',
  'text-gray-900': 'text-foreground',
  'text-gray-800': 'text-foreground',
  'text-gray-700': 'text-card-foreground',
  'text-gray-600': 'text-muted-foreground',
  'text-gray-500': 'text-muted-foreground',
  'text-gray-400': 'text-muted-foreground',
  'text-gray-300': 'text-muted-foreground',

  // Backgrounds (dark)
  'bg-slate-900': 'bg-primary',
  'hover:bg-slate-800': 'hover:bg-primary/90',
  'bg-slate-800': 'bg-primary/90',
  'bg-slate-700': 'bg-primary/80',
  'bg-slate-600': 'bg-primary/70',

  // Backgrounds (light)
  'bg-slate-200': 'bg-secondary',
  'bg-slate-100': 'bg-muted',
  'bg-slate-50': 'bg-muted/50',
  'hover:bg-slate-100': 'hover:bg-accent hover:text-accent-foreground',
  'hover:bg-slate-50': 'hover:bg-accent/50',
  'bg-white': 'bg-background',

  // Borders
  'border-slate-300': 'border-border',
  'border-slate-200': 'border-border',
  'border-slate-100': 'border-border',
  'border-slate-50': 'border-border',

  // Text white -> Primary foreground usually when inside primary bg
  'text-white': 'text-primary-foreground'
};

for (const [key, value] of Object.entries(replacements)) {
  const regex = new RegExp(`\\b${key}\\b`, 'g');
  content = content.replace(regex, value);
}

// Remove empty variants or weird artifacts if any
// (Optional cleanup)

fs.writeFileSync(file, content, 'utf-8');
console.log('Refactoring complete.');
