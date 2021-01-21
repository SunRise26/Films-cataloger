<div id="import-file-modal" class="modal" aria-hidden="true">
    <div id="import-file-modal-overlay" class="overlay" tabindex="-1" data-micromodal-close>
        <div class="container fancy-box" role="dialog" aria-modal="true" aria-labelledby="import-file-modal-title">
            <div id="import-file-modal-content">
                <form id="imprort-file-form" enctype="multipart/form-data">
                    <input type="file" id="import-file" name="import_file" class="input" />
                    <button type="submit" id="submit-file-button" class="input button">Submit</button>
                </form>
            </div>
            <div class="message-box" id="submit-file-modal-message" style="display:none">
                <span class="messages"></span>
                <button style="display:none" class="input button" id="submit-file-button-back">back</button>
                <button style="display:none" class="input button" id="submit-file-button-reload">continue</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(() => {
        const modalOverlay = $("#import-file-modal-overlay");

        const form = $("#imprort-file-form");
        const submitFileButton = $("#submit-file-button");

        const messageBox = $("#submit-file-modal-message");
        const backButton = $("#submit-file-button-back");
        const reloadButton = $("#submit-file-button-reload");

        function showMessage(message, withBackButton = false, withReloadButton = false) {
            disableModalClose();
            form.hide();
            messageBox.find('span').html(message);
            withBackButton ? backButton.show() : backButton.hide();
            withReloadButton ? reloadButton.show() : reloadButton.hide();
            messageBox.show();
        }

        function showForm() {
            enableModalClose();
            messageBox.hide();
            form.show();
        }

        function disableModalClose() {
            modalOverlay.removeAttr('data-micromodal-close');
        }

        function enableModalClose() {
            modalOverlay.attr('data-micromodal-close', true);
        }

        backButton.click(() => {
            showForm();
        });

        reloadButton.click(() => {
            location.href = '/';
        });

        function prepareImportSuccessMessage(data) {
            var message = "";
            for (const [id, values] of Object.entries(data)) {
                message += `<span class="response-message">${id}: ${values.message}</span>`
            }
            return message;
        }

        submitFileButton.click((e) => {
            e.preventDefault();

            var formData = new FormData();
            var files = $('#import-file')[0].files;

            if (files.length > 0) {
                formData.append('file', files[0]);
                showMessage("Be patient, please:) Safety over speed -_-");

                $.ajax({
                    type: "POST",
                    url: '/import',
                    data: formData,
                    contentType: false,
                    processData: false,
                    complete: (xhr) => {
                        if (xhr.status == 200) {
                            const message = prepareImportSuccessMessage(xhr.responseJSON);
                            showMessage(message, false, true);
                        } else {
                            var errorMessage = xhr.status + ': ' + xhr.statusText;
                            if (!!xhr.responseText) {
                                errorMessage += "\n" + xhr.responseText;
                            }
                            showMessage(errorMessage, true);
                        }
                    }
                });
            }
        });
    });
</script>