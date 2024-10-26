#!/bin/sh

# Get the current directory as the DOCROOT_PATH
DOCROOT_PATH="$(pwd)"

echo $DOCROOT_PATH

PLUGIN_NAME=${PLUGIN_NAME:-"plugin"}

# Construct the full path for the plugin directory
PLUGIN_DIR="${DOCROOT_PATH}/wordpress/wp-content/plugins/${PLUGIN_NAME}"

# Function to create the plugin directory
create_plugin_directory() {
    echo "Creating directory: ${PLUGIN_DIR}"
    mkdir -p "${PLUGIN_DIR}"
}

# Check if the plugin directory exists
if [ ! -d "${PLUGIN_DIR}" ]; then
    create_plugin_directory
    echo "Directory created: ${PLUGIN_DIR}"
else
    echo "Directory already exists: ${PLUGIN_DIR}"
fi
