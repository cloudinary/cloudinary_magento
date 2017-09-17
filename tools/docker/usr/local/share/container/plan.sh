#!/bin/bash

set -xe

source /usr/local/share/bootstrap/common_functions.sh
source /usr/local/share/php/common_functions.sh
load_env

symlink() {
    SRC=$1
    DST=$2
    if [ ! -L "$DST" ]; then
        ln -sr $SRC $DST
    fi
}

chown -R "${CODE_OWNER}:${CODE_GROUP}" /app/module

echo "Symlinking Cloudinary Magento module"
mkdir -p /app/public/app/code/community/Cloudinary
symlink /app/module/src/app/code/community/Cloudinary/Cloudinary /app/public/app/code/community/Cloudinary/Cloudinary
symlink /app/module/src/lib/CloudinaryExtension /app/public/lib/CloudinaryExtension
symlink /app/module/src/app/etc/modules/Cloudinary_Cloudinary.xml /app/public/app/etc/modules/Cloudinary_Cloudinary.xml
symlink /app/module/src/app/design/adminhtml/default/default/layout/cloudinary /app/public/app/design/adminhtml/default/default/layout/cloudinary
symlink /app/module/src/app/design/adminhtml/default/default/template/cloudinary /app/public/app/design/adminhtml/default/default/template/cloudinary

as_code_owner "composer install" /app/module

echo "Symlinking Cloudinary SDK"
symlink /app/module/vendor/cloudinary/cloudinary_php/src /app/public/lib/Cloudinary
