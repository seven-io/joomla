#!/bin/bash
#
# Build script for seven.io Joomla Package
# Creates: pkg_seven.zip (component + system plugin)
#          plg_vmshopper_sevensms.zip (optional VirtueMart plugin)
#

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# Version from manifest
VERSION=$(grep -oP '(?<=<version>)[^<]+' src/admin/sql/updates/mysql/*.sql 2>/dev/null | head -1 || echo "3.1.0")
VERSION=$(grep -oP '(?<=<version>)[^<]+' src/seven.xml 2>/dev/null || echo "3.1.0")

echo "Building seven.io Joomla Package v${VERSION}"
echo "============================================"

# Clean previous builds
rm -f com_seven.zip plg_system_sevensms.zip pkg_seven.zip plg_vmshopper_sevensms.zip 2>/dev/null || true
rm -rf build_tmp 2>/dev/null || true

# Build component (include manifest from src/)
echo "Creating com_seven.zip..."
cd src
zip -rq ../com_seven.zip . -x "*.DS_Store" -x "*.git*"
cd ..

# Build system plugin
echo "Creating plg_system_sevensms.zip..."
cd plugins/system/sevensms
zip -rq ../../../plg_system_sevensms.zip . -x "*.DS_Store" -x "*.git*"
cd ../../..

# Build VirtueMart plugin (separate)
echo "Creating plg_vmshopper_sevensms.zip..."
cd plugins/vmshopper/sevensms
zip -rq ../../../plg_vmshopper_sevensms.zip . -x "*.DS_Store" -x "*.git*"
cd ../../..

# Build package
echo "Creating pkg_seven.zip..."
mkdir -p build_tmp/language/en-GB build_tmp/language/de-DE
cp pkg_seven.xml build_tmp/
cp script.php build_tmp/
cp com_seven.zip build_tmp/
cp plg_system_sevensms.zip build_tmp/
cp pkg_language/en-GB/pkg_seven.ini build_tmp/language/en-GB/
cp pkg_language/de-DE/pkg_seven.ini build_tmp/language/de-DE/

cd build_tmp
zip -rq ../pkg_seven.zip . -x "*.DS_Store"
cd ..
rm -rf build_tmp

# Clean intermediate files
rm -f com_seven.zip plg_system_sevensms.zip

echo ""
echo "Build complete!"
echo ""
echo "Output files:"
ls -lh pkg_seven.zip plg_vmshopper_sevensms.zip
echo ""
echo "Installation:"
echo "  1. Install pkg_seven.zip (component + system plugin)"
echo "  2. Enable 'Seven SMS' plugin in Extensions → Plugins"
echo "  3. Optional: Install plg_vmshopper_sevensms.zip for VirtueMart"
