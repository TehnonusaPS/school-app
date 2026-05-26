const fs = require('fs');

const content = fs.readFileSync('src/modules/dev/pages/ComponentShowcase.vue', 'utf8');

// Parse script and template
const scriptMatch = content.match(/<script setup>([\s\S]*?)<\/script>/);
const templateMatch = content.match(/<template>([\s\S]*?)<\/template>/);

if (!scriptMatch || !templateMatch) {
  console.error("Could not find script or template");
  process.exit(1);
}

const scriptContent = scriptMatch[1];
const templateContent = templateMatch[1];

// We need to split the template into sections
// Each section starts with <!-- X. COMPONENT NAME -->
const sections = templateContent.split(/(?=<!-- \d+\.\s+.*?-->)/);

const header = sections[0]; // The part before the first <!-- 1. ... -->
const componentSections = sections.slice(1);

console.log(`Found ${componentSections.length} components`);

// To make it easy, we'll just put the full script in all parts? No, unused variables might cause warnings.
// Let's just output the whole thing to see.
