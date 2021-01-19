<div id="import-file-modal" class="modal" aria-hidden="true">
    <div class="overlay" tabindex="-1" data-micromodal-close>
        <div class="container fancy-box" role="dialog" aria-modal="true" aria-labelledby="import-file-modal-title">
            <div id="import-file-modal-content">
                <form id="imprort-file-form" enctype="multipart/form-data">
                    <input type="file" id="import-file" name="import_file" class="input" />
                    <button type="submit" id="submit-file-button" class="input button">Submit</button>
                </form>
            </div>
            <span id="be-patient" style="display:none;">Be patient, please:) Safety over speed -_-</span>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(() => {
        var submitFileButton = $("#submit-file-button");

        submitFileButton.click((e) => {
            e.preventDefault();

            var formData = new FormData();
            var files = $('#import-file')[0].files;

            if (files.length > 0) {
                formData.append('file', files[0]);
                $("#imprort-file-form").hide();
                $("#be-patient").show();

                $.ajax({
                    type: "POST",
                    url: '/import',
                    data: formData,
                    contentType: false,
                    processData: false,
                    complete: (xhr) => {
                        if (xhr.status == 200) {
                            location.href = '/';
                        } else {
                            var errorMessage = xhr.status + ': ' + xhr.statusText;
                            if (!!xhr.responseText) {
                                errorMessage += "\n" + xhr.responseText;
                            }
                            alert(errorMessage);
                        }
                    }
                });
            }
        });
    });
</script>