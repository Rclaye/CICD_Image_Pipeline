#!/bin/bash
set -e

echo "=========================================="
echo "CONTAINER ENTRYPOINT STARTED: $(date)"
echo "=========================================="

# Database connection details (matching wp-config.php)
DB_HOST="wordpressdbclixxjenkins.cy5secw8qpif.us-east-1.rds.amazonaws.com"
DB_USER="wordpressuser"
DB_PASSWORD="W3lcome123"
DB_NAME="wordpressdb"

# Site URL - your Route 53 hosted zone record
SITE_URL="http://ecs.stack-claye.com"

echo "DB_HOST: $DB_HOST"
echo "SITE_URL: $SITE_URL"

# Wait for database to be reachable (up to 5 minutes)
echo "Waiting for database to be reachable..."
MAX_ATTEMPTS=30
ATTEMPT=0

while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    ATTEMPT=$((ATTEMPT + 1))
    echo "Attempt $ATTEMPT of $MAX_ATTEMPTS..."
    
    # Test connection using your proven syntax
    if mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" <<EOF
SELECT 1;
EOF
    then
        echo "SUCCESS: Database is reachable!"
        
        # Update WordPress URLs using YOUR PROVEN SYNTAX
        echo "Updating WordPress URLs to: $SITE_URL"
        mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" <<EOF
USE $DB_NAME;
UPDATE wp_options SET option_value = "$SITE_URL" WHERE option_value LIKE '%ELB%';
UPDATE wp_options SET option_value = "$SITE_URL" WHERE option_name IN ('siteurl', 'home');
SELECT option_name, option_value FROM wp_options WHERE option_name LIKE '%http%';
EOF
        
        echo "WordPress URLs updated successfully!"
        break
    fi
    
    echo "Database not ready, waiting 10 seconds..."
    sleep 10
done

if [ $ATTEMPT -eq $MAX_ATTEMPTS ]; then
    echo "WARNING: Could not connect to database after $MAX_ATTEMPTS attempts"
    echo "Container will start anyway - WordPress may show incorrect URLs"
fi

echo "=========================================="
echo "ENTRYPOINT COMPLETED - Starting Apache: $(date)"
echo "=========================================="

# Execute the original command (Apache)
exec apache2-foreground
