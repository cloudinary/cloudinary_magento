# Set Magento to allow use of symlinks
as_code_owner "/app/bin/n98-magerun config:set dev/template/allow_symlink 1" /app/public

# Install modules
as_code_owner "/app/bin/n98-magerun sys:setup:run" /app/public
as_code_owner "/app/bin/n98-magerun ca:cl" /app/public