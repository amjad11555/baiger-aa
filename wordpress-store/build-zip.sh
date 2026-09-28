#!/usr/bin/env bash
# يبني ملف القالب القابل للرفع إلى ووردبريس: dist/alshami.zip
set -euo pipefail
cd "$(dirname "$0")"
for f in $(find alshami -name "*.php"); do php -l "$f" >/dev/null; done
mkdir -p dist
rm -f dist/alshami.zip
zip -rq dist/alshami.zip alshami -x "*.DS_Store" "*/node_modules/*"
echo "✓ dist/alshami.zip ($(du -h dist/alshami.zip | cut -f1))"
