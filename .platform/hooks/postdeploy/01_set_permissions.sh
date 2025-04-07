#!/bin/bash

# Set permissions for storage directory if it exists
if [ -d "/var/app/current/storage" ]; then
  chmod -R 775 /var/app/current/storage
  find /var/app/current/storage -type d -exec chmod 775 {} \;
  find /var/app/current/storage -type f -exec chmod 664 {} \;
fi

# Set proper ownership
if [ -d "/var/app/current" ]; then
  chown -R webapp:webapp /var/app/current
fi 