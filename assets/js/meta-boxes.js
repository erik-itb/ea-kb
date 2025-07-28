/**
 * Meta Boxes JavaScript
 * Handles dynamic functionality for KB and Docket meta boxes
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initResourceManagement();
        initDocketCategoryManagement();
        initSpanishContentToggle();
        initSortable();
        initIconPicker();
        initFileUpload();
    });

    /**
     * Initialize resource management for KB articles
     */
    function initResourceManagement() {
        // Add new resource
        $(document).on('click', '.eakb-add-resource-btn', function(e) {
            e.preventDefault();
            addResource();
        });

        // Remove resource
        $(document).on('click', '.eakb-remove-resource', function(e) {
            e.preventDefault();
            if (confirm(eakb_meta.strings.confirm_remove)) {
                $(this).closest('.eakb-resource-item').remove();
                reindexResources();
            }
        });

        // Toggle resource content
        $(document).on('click', '.eakb-toggle-resource', function(e) {
            e.preventDefault();
            var $content = $(this).closest('.eakb-resource-item').find('.eakb-resource-content');
            var $icon = $(this).find('.dashicons');
            
            $content.slideToggle();
            $icon.toggleClass('dashicons-arrow-down dashicons-arrow-up');
        });

        // Update resource title display when typing
        $(document).on('input', '.eakb-resource-title-input', function() {
            var title = $(this).val() || eakb_meta.strings.new_resource || 'New Resource';
            $(this).closest('.eakb-resource-item').find('.eakb-resource-title-display').text(title);
        });
    }

    /**
     * Initialize docket category management
     */
    function initDocketCategoryManagement() {
        // Add new category
        $(document).on('click', '.eakb-add-category-btn', function(e) {
            e.preventDefault();
            addDocketCategory();
        });

        // Remove category
        $(document).on('click', '.eakb-remove-category', function(e) {
            e.preventDefault();
            if (confirm(eakb_meta.strings.confirm_remove)) {
                $(this).closest('.eakb-category-item').remove();
                reindexCategories();
            }
        });

        // Toggle category content
        $(document).on('click', '.eakb-toggle-category', function(e) {
            e.preventDefault();
            var $content = $(this).closest('.eakb-category-item').find('.eakb-category-content');
            var $icon = $(this).find('.dashicons');
            
            $content.slideToggle();
            $icon.toggleClass('dashicons-arrow-down dashicons-arrow-up');
        });

        // Update category title display
        $(document).on('input', '.eakb-category-title-input', function() {
            var title = $(this).val() || 'New Category';
            $(this).closest('.eakb-category-item').find('.eakb-category-title-display').text(title);
        });

        // Add new document to category
        $(document).on('click', '.eakb-add-document', function(e) {
            e.preventDefault();
            addDocument($(this));
        });

        // Remove document
        $(document).on('click', '.eakb-remove-document', function(e) {
            e.preventDefault();
            if (confirm(eakb_meta.strings.confirm_remove)) {
                $(this).closest('.eakb-document-item').remove();
            }
        });
    }

    /**
     * Initialize Spanish content toggle
     */
    function initSpanishContentToggle() {
        $(document).on('change', '.eakb-spanish-toggle', function() {
            var $fields = $(this).closest('.eakb-spanish-content').find('.eakb-spanish-fields');
            
            if ($(this).is(':checked')) {
                $fields.slideDown();
            } else {
                $fields.slideUp();
            }
        });
    }

    /**
     * Initialize sortable functionality
     */
    function initSortable() {
        // Make resources sortable
        $('.eakb-resources-list').sortable({
            handle: '.eakb-resource-handle',
            placeholder: 'eakb-sortable-placeholder',
            update: function() {
                reindexResources();
            }
        });

        // Make categories sortable
        $('.eakb-categories-list').sortable({
            handle: '.eakb-category-handle',
            placeholder: 'eakb-sortable-placeholder',
            update: function() {
                reindexCategories();
            }
        });
    }

    /**
     * Initialize icon picker (placeholder for future implementation)
     */
    function initIconPicker() {
        $(document).on('click', '.eakb-choose-icon', function(e) {
            e.preventDefault();
            // TODO: Implement icon picker modal
            alert('Icon picker will be implemented in the next phase');
        });

        $(document).on('click', '.eakb-remove-icon', function(e) {
            e.preventDefault();
            $('#eakb_featured_icon').val('');
            $('.eakb-icon-preview').empty();
            $(this).hide();
        });
    }

    /**
     * Initialize file upload functionality
     */
    function initFileUpload() {
        $(document).on('click', '.eakb-upload-file', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $input = $button.siblings('input[type="url"]');
            
            // Create WordPress media uploader
            var frame = wp.media({
                title: 'Select or Upload File',
                button: {
                    text: 'Use this file'
                },
                multiple: false
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $input.val(attachment.url);
                
                // Try to auto-detect file type based on URL
                var fileExtension = attachment.url.split('.').pop().toLowerCase();
                var $typeSelect = $input.closest('tr').find('select[name*="[type]"]');
                
                if ($typeSelect.length) {
                    var typeMapping = {
                        'pdf': 'pdf',
                        'doc': 'doc',
                        'docx': 'doc',
                        'xls': 'sheet',
                        'xlsx': 'sheet',
                        'csv': 'sheet',
                        'ppt': 'presentation',
                        'pptx': 'presentation',
                        'mp4': 'video',
                        'avi': 'video',
                        'mov': 'video',
                        'mp3': 'audio',
                        'wav': 'audio',
                        'jpg': 'image',
                        'jpeg': 'image',
                        'png': 'image',
                        'gif': 'image'
                    };
                    
                    if (typeMapping[fileExtension]) {
                        $typeSelect.val(typeMapping[fileExtension]);
                    }
                }
            });

            frame.open();
        });
    }

    /**
     * Add new resource
     */
    function addResource() {
        var $container = $('.eakb-resources-list');
        var index = $container.find('.eakb-resource-item').length;
        var template = $('#eakb-resource-template').html();
        
        // Replace template placeholders
        template = template.replace(/\{\{INDEX\}\}/g, index);
        
        var $newItem = $(template);
        $container.append($newItem);
        
        // Show the content immediately for new items
        $newItem.find('.eakb-resource-content').show();
        $newItem.find('.eakb-toggle-resource .dashicons').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
        
        // Focus on title field
        $newItem.find('.eakb-resource-title-input').focus();
        
        // Update sortable
        $container.sortable('refresh');
    }

    /**
     * Add new docket category
     */
    function addDocketCategory() {
        var $container = $('.eakb-categories-list');
        var index = $container.find('.eakb-category-item').length;
        var template = $('#eakb-category-template').html();
        
        // Replace template placeholders
        template = template.replace(/\{\{INDEX\}\}/g, index);
        
        var $newItem = $(template);
        $container.append($newItem);
        
        // Show the content immediately for new items
        $newItem.find('.eakb-category-content').show();
        $newItem.find('.eakb-toggle-category .dashicons').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-up');
        
        // Focus on title field
        $newItem.find('.eakb-category-title-input').focus();
        
        // Update sortable
        $container.sortable('refresh');
    }

    /**
     * Add new document to category
     */
    function addDocument($addButton) {
        var $categoryItem = $addButton.closest('.eakb-category-item');
        var $documentsList = $categoryItem.find('.eakb-documents-list');
        var categoryIndex = $categoryItem.data('index');
        var docIndex = $documentsList.find('.eakb-document-item').length;
        
        var documentHtml = `
            <div class="eakb-document-item">
                <div class="eakb-document-fields">
                    <input type="text" 
                           name="eakb_categories[${categoryIndex}][documents][${docIndex}][title]" 
                           placeholder="Document title" 
                           class="regular-text">
                    
                    <select name="eakb_categories[${categoryIndex}][documents][${docIndex}][type]">
                        <option value="pdf">PDF</option>
                        <option value="doc">Word Doc</option>
                        <option value="sheet">Spreadsheet</option>
                        <option value="presentation">Presentation</option>
                        <option value="external">External Link</option>
                    </select>
                    
                    <input type="url" 
                           name="eakb_categories[${categoryIndex}][documents][${docIndex}][url]" 
                           placeholder="Document URL" 
                           class="regular-text">
                    
                    <input type="date" 
                           name="eakb_categories[${categoryIndex}][documents][${docIndex}][date]" 
                           class="regular-text">
                    
                    <button type="button" class="button-link eakb-remove-document">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </div>
            </div>
        `;
        
        $documentsList.append(documentHtml);
        
        // Focus on the new document title field
        $documentsList.find('.eakb-document-item').last().find('input[type="text"]').first().focus();
    }

    /**
     * Reindex resources after sorting/removing
     */
    function reindexResources() {
        $('.eakb-resources-list .eakb-resource-item').each(function(index) {
            $(this).attr('data-index', index);
            
            // Update all input names
            $(this).find('input, select, textarea').each(function() {
                var name = $(this).attr('name');
                if (name && name.indexOf('eakb_resources[') === 0) {
                    var newName = name.replace(/eakb_resources\[\d+\]/, 'eakb_resources[' + index + ']');
                    $(this).attr('name', newName);
                }
            });
        });
    }

    /**
     * Reindex categories after sorting/removing
     */
    function reindexCategories() {
        $('.eakb-categories-list .eakb-category-item').each(function(categoryIndex) {
            $(this).attr('data-index', categoryIndex);
            
            // Update category input names
            $(this).find('input, select, textarea').each(function() {
                var name = $(this).attr('name');
                if (name && name.indexOf('eakb_categories[') === 0) {
                    var newName = name.replace(/eakb_categories\[\d+\]/, 'eakb_categories[' + categoryIndex + ']');
                    $(this).attr('name', newName);
                }
            });
            
            // Reindex documents within this category
            $(this).find('.eakb-document-item').each(function(docIndex) {
                $(this).find('input, select').each(function() {
                    var name = $(this).attr('name');
                    if (name && name.indexOf('documents[') > -1) {
                        var newName = name.replace(/documents\[\d+\]/, 'documents[' + docIndex + ']');
                        $(this).attr('name', newName);
                    }
                });
            });
        });
    }

    /**
     * Show loading state for buttons
     */
    function showButtonLoading($button, text) {
        text = text || 'Loading...';
        $button.prop('disabled', true).text(text);
    }

    /**
     * Hide loading state for buttons
     */
    function hideButtonLoading($button, originalText) {
        $button.prop('disabled', false).text(originalText);
    }

})(jQuery);