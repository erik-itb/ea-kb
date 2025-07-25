#!/bin/bash

# Script to check code with relaxed settings
echo "🔍 Running code checks with relaxed settings..."

# Check if phpcs is available
if [ -f "vendor/bin/phpcs" ]; then
    echo "📋 Running PHPCS with custom ruleset..."
    ./vendor/bin/phpcs --standard=./phpcs.xml --report=summary .
    echo ""
elif command -v phpcs &> /dev/null; then
    echo "📋 Running PHPCS with custom ruleset (global installation)..."
    phpcs --standard=./phpcs.xml --report=summary .
    echo ""
else
    echo "⚠️  PHPCS not found. Install with: composer install"
fi

# Check if phpstan is available
if command -v phpstan &> /dev/null; then
    echo "🔬 Running PHPStan with relaxed settings..."
    phpstan analyse --configuration=phpstan.neon --no-progress
    echo ""
elif [ -f "vendor/bin/phpstan" ]; then
    echo "🔬 Running PHPStan with relaxed settings..."
    ./vendor/bin/phpstan analyse --configuration=phpstan.neon --no-progress
    echo ""
else
    echo "⚠️  PHPStan not found. Install with: composer install"
fi

# Check if php-cs-fixer is available
if command -v php-cs-fixer &> /dev/null; then
    echo "🎨 Running PHP CS Fixer (dry run)..."
    php-cs-fixer fix --config=.php-cs-fixer.dist.php --dry-run --diff
    echo ""
else
    echo "⚠️  PHP CS Fixer not found. Install with: composer global require friendsofphp/php-cs-fixer"
fi

echo "✅ Code check complete!"
echo ""
echo "💡 Tips:"
echo "   - Most formatting warnings are now disabled"
echo "   - PHPStan level reduced from 5 to 3"
echo "   - VSCode auto-formatting is disabled"
echo "   - Only functional issues should be reported"
