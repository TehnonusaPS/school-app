const fs = require('fs');

let content = fs.readFileSync('src/modules/keuangan/pages/Spp.vue', 'utf8');

// A simple stack-based parser to find and replace <div> with <Card>
function transformCards(html) {
    let result = '';
    let i = 0;
    
    // We'll keep track of the open tags to know when a div we transformed to Card closes.
    // Each element in stack is a boolean: true if we changed this div to Card, false otherwise.
    let divStack = [];
    
    while (i < html.length) {
        // Look for next tag
        if (html.slice(i, i+4) === '<div') {
            // Find end of the opening tag
            let tagEnd = html.indexOf('>', i);
            let tag = html.slice(i, tagEnd + 1);
            
            // Check if it's a raw card div
            if (tag.includes('bg-card') && !tag.includes('Card')) {
                // Remove bg-card, rounded-xl, border, border-border as Card provides them
                // Wait, Shadcn Card provides ring-1 ring-foreground/10 bg-card rounded-xl text-card-foreground
                let newTag = tag.replace('<div', '<Card')
                               .replace(/bg-card/g, '')
                               .replace(/rounded-xl/g, '')
                               .replace(/border-border/g, '')
                               .replace(/border-l-4/g, '') // Keep it? Let's leave custom classes alone, just change div to Card.
                               .replace(/border/g, '')
                               .replace(/\s+/g, ' '); // Clean up extra spaces
                
                // Add class cleanup
                result += newTag;
                divStack.push(true);
            } else {
                result += tag;
                divStack.push(false);
            }
            i = tagEnd + 1;
        } 
        else if (html.slice(i, i+6) === '</div>') {
            let isCard = divStack.pop();
            if (isCard) {
                result += '</Card>';
            } else {
                result += '</div>';
            }
            i += 6;
        } 
        else {
            result += html[i];
            i++;
        }
    }
    
    return result;
}

let transformed = transformCards(content);
// Also make sure Card is imported
if (!transformed.includes("import { Card")) {
    transformed = transformed.replace(/import { (.*?) } from '@\/components\/ui\/card'/g, "import { Card, $1 } from '@/components/ui/card'");
}
fs.writeFileSync('src/modules/keuangan/pages/Spp.vue', transformed);
console.log("Transformed cards");
