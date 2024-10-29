jQuery(document).ready(function ($) {

    $('#spwai-generate').on('click', function () {
        var prompt = $('#spwai-prompt').val();

        $('#spwai-error-message').html(''); // error message

        // Validate that the prompt is not empty
        if (!prompt.trim()) {
            $('#spwai-error-message').html('Error: Please enter Product keyword.');
            return; // Do not proceed with the AJAX request
        }

        // generate text from open ai for generateFields array values
        var generateFields = ['title', 'description', 'shortdescription'];
        // Filter the array based on checkboxes
        generateFields = generateFields.filter(function (item) {
            return $('#spwai-check-' + item).prop('checked');
        });

        // Check if no checkboxes are checked
        if (generateFields.length === 0) {
            $('#spwai-error-message').html('Please select at least one checkbox!');
            return;
        }

        var step = 0; // first field : title
        generate_text_from_openai(prompt, generateFields, step);

    });


    // save generated values 
    $('#spwai-apply').on('click', function () {
        // validate fields are selected
        var generateFields = ['title', 'description', 'shortdescription'];
        selectedFields = generateFields.filter(function (item) { // Filter based on checkboxes
            return $('#spwai-check-' + item).prop('checked');
        });
        // Check if no checkboxes are checked
        if (selectedFields.length === 0) {
            $('#spwai-error-message').html('Failed to save: No Checkbox are selected!');
            var errorMessageElement = document.getElementById('spwai-error-message');
            errorMessageElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        } else {
            saveGeneratedValues(selectedFields);
        }

    });

    // generate text from open ai
    function generate_text_from_openai(prompt, generateFields, step) {
        var nonce = $('#spwai-nonce').val();
        // var postId = $('#post_ID').val();

        $('#spwai-loader').show(); // show loading

        var currentField = generateFields[step]; // field to generate
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            dataType: 'json',
            data: {
                action: 'spwai_generate_text',
                prompt: prompt,
                field: currentField,
                nonce: nonce
            },
            beforeSend: function () {
                // Show the loader before making the AJAX request
                $('#spwai-loader').show();
            },
            success: function (response) {
                console.log(response);
                if (response.status === 'success') {
                    // $('#spwai-' + currentField).val(response.message);
                    // displayMessage(currentField, response.message);

                    let messageElement = $('#spwai-' + currentField);
                    displayMessage(messageElement, response.message);

                    step++;
                    let fieldsCount = generateFields.length;
                    if (step < fieldsCount) {
                        generate_text_from_openai(prompt, generateFields, step);
                    } else {
                        $('#spwai-loader').hide();
                    }
                } else {
                    // failed case
                    $('#spwai-error-message').html(response.message);
                    $('#spwai-loader').hide();
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Handle AJAX request errors
                console.error('AJAX request failed: ' + textStatus, errorThrown);
                $('#spwai-error-message').html('Error: Unable to fetch data.');
                $('#spwai-loader').hide();
            },
            // complete: function() {
            //     $('#spwai-loader').hide(); // hide loading
            // }
        });
    }

    // generate text from open ai
    function generate_text_from_openai_variation(prompt, generateFields, step, varMetabox) {
        var nonce = $('#spwai-nonce').val();
        // var postId = $('#post_ID').val();

        varMetabox.find('.spwai-loader').show();
        var errorElement = varMetabox.find('.spwai-error-message');
        // $('#spwai-loader').show(); // show loading

        var currentField = generateFields[step]; // field to generate
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            dataType: 'json',
            data: {
                action: 'spwai_generate_text',
                prompt: prompt,
                field: currentField,
                nonce: nonce
            },
            beforeSend: function () {
                // Show the loader before making the AJAX request
                varMetabox.find('.spwai-loader').show();
            },
            success: function (response) {
                console.log(response);
                if (response.status === 'success') {
                    // varMetabox.find('.spwai-description').val(response.message);
                    // displayMessage(currentField, response.message);
                    displayMessage(varMetabox.find('.spwai-description'), response.message);

                    step++;
                    let fieldsCount = generateFields.length;
                    if (step < fieldsCount) {
                        generate_text_from_openai_variation(prompt, generateFields, step, varMetabox);
                    } else {
                        varMetabox.find('.spwai-loader').hide();
                    }
                } else {
                    // failed case
                    errorElement.html(response.message);
                    varMetabox.find('.spwai-loader').hide();
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Handle AJAX request errors
                console.error('AJAX request failed: ' + textStatus, errorThrown);
                errorElement.html('Error: Unable to fetch data.');
                varMetabox.find('.spwai-loader').hide();
            },
            // complete: function() {
            //     $('#spwai-loader').hide(); // hide loading
            // }
        });
    }


    // save generated values 
    function saveGeneratedValues(selectedFields) {
        // Fetch values from input fields for selected fields
        var fieldsArr = {};
        selectedFields.forEach(function (field) {
            fieldsArr[field] = $('#spwai-' + field).val();
        });

        // Check if any of the values is not empty
        if (Object.values(fieldsArr).some(value => value !== '')) {
            var nonce = $('#spwai-nonce').val();
            var postId = $('#post_ID').val();

            $('#spwai-error-message').html('');
            var isConfirmed = window.confirm('Are you sure you want to save?');
            if (isConfirmed) {
                // Send AJAX request to save values
                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        action: 'spwai_save_product_data',
                        nonce: nonce,
                        product_id: postId,
                        fields: fieldsArr
                    },
                    success: function (response) {
                        console.log(response);
                        if (response.status === 'success') {
                            // update success
                            location.reload(true);
                        } else {
                            // update failed
                            alert(response.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // Handle AJAX request errors
                        console.error('AJAX request failed: ' + textStatus, errorThrown);
                        // Display an error message if needed
                        alert('Error saving values.');
                    }
                });
            }

        } else {
            $('#spwai-error-message').html('Failed to save: Generated fields are empty!');
            var errorMessageElement = document.getElementById('spwai-error-message');
            errorMessageElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

    }


    // save variation description 
    function saveVariationData(variationId, varMetabox, generatedDesc) {
        var errorElement = varMetabox.find('.spwai-error-message');
        // Check if values is not empty
        if (variationId && generatedDesc) {
            errorElement.html('');
            var nonce = $('#spwai-nonce').val();
            // Send AJAX request to save values
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    action: 'spwai_save_product_data',
                    nonce: nonce,
                    variation_id: variationId,
                    description: generatedDesc
                },
                success: function (response) {
                    console.log(response);
                    if (response.status === 'success') {
                        // update success
                        // scroll top to description field
                        var offset = 300;
                        $('html, body').animate({
                            scrollTop: varMetabox.offset().top - offset
                        }, 1000);
                    } else {
                        // update failed
                        errorElement.html(response.message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // Handle AJAX request errors
                    console.error('AJAX request failed: ' + textStatus, errorThrown);
                    // Display an error message if needed
                    errorElement.html("Save failed: ajax error!");
                }
            });

        } else {
            errorElement.html('Failed to save: Generated field empty!');
        }
    }



    // function displayMessage(messageElement, message) {
    //     // let elementID = 'spwai-' + msgElement;
    //     // const messageElement = document.getElementById(elementID);
    //     messageElement.value = ''; // remove existing value from input field

    //     // Split the message into paragraphs
    //     const paragraphs = message.split(/\n+/);

    //     let currentParagraphIndex = 0;

    //     function typeParagraph() {
    //         if (currentParagraphIndex < paragraphs.length) {
    //             // Split the current paragraph into words
    //             const words = paragraphs[currentParagraphIndex].split(/\s+/);
    //             let currentWordIndex = 0;

    //             function typeWord() {
    //                 if (currentWordIndex < words.length) {
    //                     // Display the current word with spaces
    //                     messageElement.value += (currentWordIndex === 0 ? '' : ' ') + words[currentWordIndex];
    //                     currentWordIndex++;
    //                     setTimeout(typeWord, 100); // Adjust the typing speed between words
    //                 } else {
    //                     // Move to the next paragraph
    //                     currentParagraphIndex++;
    //                     // Add a line break between paragraphs
    //                     messageElement.value += '\n\n';
    //                     // Start typing the next paragraph
    //                     typeParagraph();
    //                 }
    //             }

    //             // Start typing the current paragraph
    //             typeWord();
    //         } else {
    //             // Scroll to the bottom of the textarea
    //             messageElement.scrollTop = messageElement.scrollHeight;
    //         }
    //     }

    //     // Start typing animation
    //     typeParagraph();
    // }


    function displayMessage(messageElement, message) {
        messageElement.val(''); // remove existing value from input field

        // Split the message into paragraphs
        const paragraphs = message.split(/\n+/);

        let currentParagraphIndex = 0;

        function typeParagraph() {
            if (currentParagraphIndex < paragraphs.length) {
                // Split the current paragraph into words
                const words = paragraphs[currentParagraphIndex].split(/\s+/);
                let currentWordIndex = 0;

                function typeWord() {
                    if (currentWordIndex < words.length) {
                        // Display the current word with spaces
                        messageElement.val(messageElement.val() + (currentWordIndex === 0 ? '' : ' ') + words[currentWordIndex]);
                        currentWordIndex++;
                        setTimeout(typeWord, 100); // Adjust the typing speed between words
                    } else {
                        // Move to the next paragraph
                        currentParagraphIndex++;
                        // Add a line break between paragraphs
                        messageElement.val(messageElement.val() + '\n\n');
                        // Start typing the next paragraph
                        typeParagraph();
                    }
                }

                // Start typing the current paragraph
                typeWord();
            } else {
                // Scroll to the bottom of the textarea
                messageElement.scrollTop(messageElement[0].scrollHeight);
            }
        }

        // Start typing animation
        typeParagraph();
    }



    //=============== Variation product ===========

    // Generate Content - click event
    $('.woocommerce_variations').on('click', '.spwai-variation-meta-box .spwai-generate', function () { // event delegation for dynamical elements
        var loop = $(this).data("loop");
        var varMetabox = $(this).closest('.spwai-variation-meta-box');
        var errorElement = varMetabox.find('.spwai-error-message');
        errorElement.html(''); // remove error message

        // get product keywords
        var prompt = varMetabox.find('.spwai-prompt').val();

        // validate empty
        if (!prompt.trim()) {
            errorElement.html('Error: Please enter Product keyword.');
            return; // Do not proceed with the AJAX request
        }

        // generate text from open ai for generateFields array values
        var generateFields = ['var-description'];
        var step = 0; // first field : title
        generate_text_from_openai_variation(prompt, generateFields, step, varMetabox);

    });

    // Save data - click event
    // $('.woocommerce_variations').on('click', '.spwai-variation-meta-box .spwai-apply', function () { // event delegation for dynamical elements
    //     var loop = $(this).data("loop");
    //     var errorElement = $(this).closest('.spwai-variation-meta-box').find('.spwai-error-message');
    //     errorElement.html(''); // remove error message

    //     // get generated description
    //     var generatedDesc = $(this).closest('.spwai-variation-meta-box').find('.spwai-description').val();

    //     // validate empty
    //     if (!generatedDesc.trim()) {
    //         errorElement.html('Failed to save: Generated description is empty.');
    //         return; // Do not proceed with the AJAX request
    //     }

    //     // update variation description
    //     var variationDesc = $(this).closest('.woocommerce_variation').find('#variable_description' + loop);
    //     variationDesc.val(generatedDesc).change();

    //     // Save variation Data
    //     var variationSaveBtn = $(this).closest('#variable_product_options').find('.save-variation-changes');
    //     // variationSaveBtn.prop('disabled', false);
    //     variationSaveBtn.trigger('click'); // Trigger a click event on 'Save Changes' button
    // });



    $('.woocommerce_variations').on('click', '.spwai-variation-meta-box .spwai-apply', function () { // event delegation for dynamical elements
        var loop = $(this).data("loop");
        var varMetabox = $(this).closest('.spwai-variation-meta-box');
        var errorElement = varMetabox.find('.spwai-error-message');
        errorElement.html(''); // remove error message

        // get generated description
        var generatedDesc = varMetabox.find('.spwai-description').val();

        // validate empty
        if (!generatedDesc.trim()) {
            errorElement.html('Failed to save: Generated description is empty.');
            return; // Do not proceed with the AJAX request
        }

        var isConfirmed = window.confirm('Are you sure you want to save description?');
        if (isConfirmed) {
            // get closest variation parent
            var thisVariation = $(this).closest('.woocommerce_variation');
            // replace variation description with generated
            thisVariation.find('#variable_description' + loop).val(generatedDesc);
            // get variation id
            var variationId = thisVariation.find('.variable_post_id').val();

            // call save function
            saveVariationData(variationId, varMetabox, generatedDesc);
        }

    });


});
