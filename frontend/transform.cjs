const fs = require('fs');

let content = fs.readFileSync('src/modules/keuangan/pages/Spp.vue', 'utf8');

function transformCards(html) {
    let result = '';
    let i = 0;
    
    let divStack = [];
    
    while (i < html.length) {
        if (html.slice(i, i+4) === '<div') {
            let tagEnd = html.indexOf('>', i);
            let tag = html.slice(i, tagEnd + 1);
            
            if (tag.includes('bg-card') && !tag.includes('Card')) {
                // Keep the classes but remove those that conflict with Card
                let newTag = tag.replace('<div', '<Card')
                               .replace(/bg-card/g, '')
                               .replace(/rounded-xl/g, '')
                               .replace(/border-border/g, '')
                               .replace(/border/g, '')
                               .replace(/shadow-sm/g, '')
                               .replace(/\s+/g, ' ')
                               .replace(/class=" "/g, ''); 
                
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
fs.writeFileSync('src/modules/keuangan/pages/Spp.vue', transformed);
console.log("Transformed cards");
