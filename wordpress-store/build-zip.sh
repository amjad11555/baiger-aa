#!/usr/bin/env bash
# يبني ملف القالب القابل للرفع إلى ووردبريس: dist/zad.zip
set -euo pipefail
cd "$(dirname "$0")"
for f in $(find zad -name "*.php"); do php -l "$f" >/dev/null; done
mkdir -p dist
rm -f dist/zad.zip
zip -rq dist/zad.zip zad -x "*.DS_Store" "*/node_modules/*"
echo "✓ dist/zad.zip ($(du -h dist/zad.zip | cut -f1))"
