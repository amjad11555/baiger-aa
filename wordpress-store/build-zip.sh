#!/usr/bin/env bash
# يبني ملف القالب القابل للرفع إلى ووردبريس: dist/lazza.zip
set -euo pipefail
cd "$(dirname "$0")"
for f in $(find lazza -name "*.php"); do php -l "$f" >/dev/null; done
mkdir -p dist
rm -f dist/lazza.zip
zip -rq dist/lazza.zip lazza -x "*.DS_Store" "*/node_modules/*"
echo "✓ dist/lazza.zip ($(du -h dist/lazza.zip | cut -f1))"
