(function(window, document) {
    'use strict';

    if ($$('head')[0]) {
        $$('head')[0].appendChild(new Element('script', {
            type: 'text/javascript',
            src: 'https://media-library.cloudinary.com/global/all.js'
        }));
        $$('head')[0].appendChild(new Element('script', {
            type: 'text/javascript',
            src: 'https://cdnjs.cloudflare.com/ajax/libs/es6-promise/4.1.1/es6-promise.auto.min.js'
        }));
        $$('head')[0].appendChild(new Element('script', {
            type: 'text/javascript',
            src: 'https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.js'
        }));
        $$('head')[0].appendChild(new Element('script', {
            type: 'text/javascript',
            src: 'https://cdnjs.cloudflare.com/ajax/libs/axios/0.15.2/axios.js'
        }));
    }

    window._cloudinary_transformation_tab_images = window._cloudinary_transformation_tab_images || {};

    window.CloudinaryMediaLibrary = Class.create();
    window.CloudinaryMediaLibrary.prototype = {

        options: {
            buttonSelector: null,
            triggerSelector: null, // #media_gallery_content .image.image-placeholder > .uploader' / '.media-gallery-modal'
            triggerEvent: null, // 'addItem' / 'fileuploaddone'
            callbackHandler: null,
            callbackHandlerMethod: null,
            imageParamName: 'image',
            cloudinaryMLoptions: {}, // Options for Cloudinary-ML createMediaLibrary()
            cloudinaryMLshowOptions: {}, // Options for Cloudinary-ML show()
            cldMLid: 0,
            useDerived: true,
            addTmpExtension: false
        },

        eventsAttached: false,
        initialized: false,

        /**
         * @constructor
         */
        initialize: function(config) {
            this.setOptions(config);
            this.attachEvents();
            this.cldInitialize();
            console.log(this.options);
        },

        setOptions: function(config) {
            if (typeof config === 'object') {
                for (var key in config) {
                    if (!config.hasOwnProperty(key)) continue;
                    this.options[key] = config[key];
                }
            }
        },

        cldInitialize: function(force) {
            if ((!this.initialized || force) && typeof cloudinary === 'object') {
                var widget = this;
                window.cloudinary_ml = window.cloudinary_ml || [];
                this.options.cldMLid = this.options.cldMLid || 0;
                if (typeof window.cloudinary_ml[this.options.cldMLid] === "undefined") {
                    this.cloudinary_ml = window.cloudinary_ml[this.options.cldMLid] = cloudinary.createMediaLibrary(
                        this.options.cloudinaryMLoptions, {
                            insertHandler: function(data) {
                                return widget.cloudinaryInsertHandler(data);
                            }
                        }
                    );
                } else {
                    this.cloudinary_ml = window.cloudinary_ml[this.options.cldMLid];
                }
                this.initialized = true;
            }
        },

        /**
         * Attach all types of events
         */
        attachEvents: function() {
            if (!this.eventsAttached) {
                if ($$(this.options.buttonSelector).length) {
                    $$(this.options.buttonSelector).first().on('click', this.openMediaLibrary.bind(this));
                }
            }
        },

        /**
         * Fired on trigger "openMediaLibrary"
         */
        openMediaLibrary: function() {
            this.cldInitialize();
            this.cloudinary_ml.show(this.options.cloudinaryMLshowOptions);
        },

        /**
         * Fired on trigger "cloudinaryInsertHandler"
         */
        cloudinaryInsertHandler: function(data) {
            var widget = this;

            data.assets.forEach(asset => {
                //console.log(asset);
                if (widget.options.imageUploaderUrl) {
                    asset.asset_url = asset.asset_image_url = asset.secure_url;
                    if (asset.derived && asset.derived[0] && asset.derived[0].secure_url) {
                        asset.asset_derived_url = asset.asset_derived_image_url = asset.derived[0].secure_url;
                        asset.free_transformation = asset.asset_derived_image_url
                            .replace(new RegExp('^.*cloudinary.com/' + this.options.cloudinaryMLoptions.cloud_name + '/' + asset.resource_type + '/' + asset.type + '/'), '')
                            .replace(/\.[^/.]+$/, '')
                            .replace(new RegExp('\/' + asset.public_id + '$'), '')
                            .replace(new RegExp('\/v[0-9]{1,10}$'), '')
                            .replace(new RegExp('\/'), ',');
                        if (widget.options.useDerived) {
                            asset.asset_url = asset.asset_image_url = asset.derived[0].secure_url;
                        }
                    }
                    new Ajax.Request(widget.options.imageUploaderUrl, {
                        method: 'post',
                        parameters: {
                            asset: asset,
                            remote_image: asset.asset_image_url,
                            param_name: widget.options.imageParamName,
                            form_key: window.FORM_KEY
                        },
                        asynchronous: false,
                        onSuccess: function(transport, file) {
                            file = transport.responseJSON || {};
                            if (file.file && !file.error) {
                                var context = (asset.context && asset.context.custom) ? asset.context.custom : {};
                                if (asset.resource_type === "image") {
                                    file.media_type = "image";
                                    file.label = asset.label = context.alt || context.caption || asset.public_id || "";
                                    if (widget.options.addTmpExtension && !/\.tmp$/.test(file.file)) {
                                        file.file = file.file + '.tmp';
                                    }
                                }
                                file.free_transformation = asset.free_transformation;
                                file.asset_derived_image_url = asset.asset_derived_image_url;
                                file.image_url = asset.asset_image_url;
                                file.cloudinary_asset = asset;
                                //console.log(file);
                                widget.successTrigger(file);

                                file.error = false;
                                file.id = file.id || Math.random().toString(36).substr(2, 16);
                                file.orig_free_transformation = file.free_transformation;
                                file.image_url = asset.asset_derived_image_url;
                                window._cloudinary_transformation_tab_images[file.id] = file;
                                if (typeof window.cloudinaryTransformationTabApp === 'object') {
                                    window.cloudinaryTransformationTabApp.addImage(file);
                                }

                            } else {
                                alert($.mage.__('An error occured during ' + asset.resource_type + ' insert!'));
                                console.error(file);
                            }
                        },
                        onFailure: function(transport) {
                            alert($.mage.__('An error occured during ' + asset.resource_type + ' insert!'));
                            console.error(transport);
                        }
                    });
                }
            });
        },

        /**
         * Trigger success actions after asset upload
         */
        successTrigger: function(file) {
            if (this.options.callbackHandler == 'window.media_gallery_contentJsObject' && this.options.callbackHandlerMethod == 'handleUploadComplete' && window.media_gallery_contentJsObject && typeof window.media_gallery_contentJsObject.handleUploadComplete === 'function') {
                window.media_gallery_contentJsObject.handleUploadComplete([{
                    response: JSON.stringify(file)
                }]);
            }
            if (this.options.callbackHandler == 'window.MediabrowserInstance' && this.options.callbackHandlerMethod == 'selectFolder' && window.MediabrowserInstance && typeof window.MediabrowserInstance.selectFolder === 'function') {
                window.MediabrowserInstance.selectFolder(window.MediabrowserInstance.currentNode);
            }
        },

    };
})(window, document);