#!/usr/bin/env bash
# يبني ملف القالب القابل للرفع إلى ووردبريس: dist/biskato.zip
set -euo pipefail
cd "$(dirname "$0")"
for f in $(find biskato -name "*.php"); do php -l "$f" >/dev/null; done
mkdir -p dist
rm -f dist/biskato.zip
zip -rq dist/biskato.zip biskato -x "*.DS_Store" "*/node_modules/*"
echo "✓ dist/biskato.zip ($(du -h dist/biskato.zip | cut -f1))"
