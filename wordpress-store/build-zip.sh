#!/usr/bin/env bash
# يبني ملف القالب القابل للرفع إلى ووردبريس: dist/maria.zip
set -euo pipefail
cd "$(dirname "$0")"
for f in $(find maria -name "*.php"); do php -l "$f" >/dev/null; done
mkdir -p dist
rm -f dist/maria.zip
zip -rq dist/maria.zip maria -x "*.DS_Store" "*/node_modules/*"
echo "✓ dist/maria.zip ($(du -h dist/maria.zip | cut -f1))"
