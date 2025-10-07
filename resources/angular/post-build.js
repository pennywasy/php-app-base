const fs = require('fs');
const path = require('path');

// 1. Читаем собранный index.html
const angularAppPath = path.join(__dirname, '../../resources/views/angular-app.php');
let content = fs.readFileSync(angularAppPath, 'utf8');

// 2. Заменяем пути
content = content.replace(/(href|src)="([^"]+)"/g, (match, attr, filePath) => {
  if (!filePath.startsWith('http')) {
    return `${attr}="<?= View::asset('/assets${filePath.startsWith('/') ? '' : '/'}${filePath}') ?>"`;
  }
  return match;
});

// 3. Добавляем PHP-шапку
content = `<?php namespace App\Core; ?>\n${content}`;

// 4. Сохраняем
fs.writeFileSync(angularAppPath, content);
