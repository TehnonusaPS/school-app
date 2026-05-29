const fs = require('fs');
let content = fs.readFileSync('src/modules/keuangan/pages/Spp.vue', 'utf8');

content = content.replace(/hover:-primary/g, 'hover:border-primary')
                 .replace(/hover:-blue-500/g, 'hover:border-blue-500')
                 .replace(/hover:-emerald-500/g, 'hover:border-emerald-500');

fs.writeFileSync('src/modules/keuangan/pages/Spp.vue', content);
