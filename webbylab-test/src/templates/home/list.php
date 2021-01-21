<div class="list">
    <?php if (count($this->getData('films'))) : ?>
        <div id="films-box">
            <?php foreach ($this->getData('films') as $film_data) : ?>
                <?php $title = htmlspecialchars($film_data['title']); ?>
                <div class="film-box">
                    <div class="film-data">
                        <div class="box-row">
                            <span class="id">ID: <?= $film_data['id'] ?></span>
                            <button type="button" class="delete-film" data-id="<?= $film_data['id'] ?>" data-title="<?= $title ?>">delete</button>
                        </div>
                        <span class="title">TITLE: <?= $title ?></span>
                        <span class="release-year">RELEASE YEAR: <?= $film_data['release_year'] ?></span>
                        <span class="format">FORMAT: <?= $film_data['format_title'] ?></span>
                    </div>
                    <?php if (!empty($film_data['actors'])) : ?>
                        <div>
                            <?php $actors = array_map('htmlspecialchars', $film_data['actors']); ?>
                            <span>ACTORS: <?= implode(', ', $actors); ?></span>
                        </div>
                    <?php endif ?>
                </div>
            <?php endforeach ?>
        </div>
    <?php else : ?>
        <span class="list-message">NO FILMS FOUNDED</span>
    <?php endif ?>
    <div id="list-message-box" style="display:none;">
        <span class="list-message"></span>
        <button class="input button" id="list-message-back">back</button>
    </div>
    <div id="delete-verification-box" style="display:none;">
        <span class="list-message"></span>
        <div class="delete-actions">
            <button class="input button" id="delete-approve">yes</button>
            <button class="input button" id="delete-decline">no</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(() => {
        const deleteButtons = $(".delete-film");
        const listBox = $("#films-box");

        const deleteBox = $("#delete-verification-box");
        const deleteDeclineButton = $("#delete-decline");
        const deleteApproveButton = $("#delete-approve");

        const messageBox = $("#list-message-box");
        const messageBoxBackButton = $("#list-message-back");

        var filmToDelete = null;

        function hideAllBoxes() {
            listBox.hide();
            deleteBox.hide();
            messageBox.hide();
        }

        function showMessage(message, withBackButton) {
            hideAllBoxes()
            messageBox.find('.list-message').html(message);
            withBackButton ? messageBoxBackButton.show() : messageBoxBackButton.hide();
            messageBox.show();
        }

        function showList() {
            hideAllBoxes();
            listBox.show();
        }

        function showDeleteBox() {
            hideAllBoxes();
            deleteBox.find('.list-message').html(`Delete "${filmToDelete.title}"?`);
            deleteBox.show();
        }

        messageBoxBackButton.click(() => {
            showList();
        });

        deleteDeclineButton.click(() => {
            showList();
        })

        deleteApproveButton.click(() => {
            showMessage("DELETING...");

            $.ajax({
                type: "POST",
                url: '/film/delete',
                data: {
                    id: filmToDelete.id
                },
                complete: (xhr) => {
                    if (xhr.status == 200) {
                        location.href = window.location.href;
                    } else {
                        var errorMessage = xhr.status + ': ' + xhr.statusText
                        showMessage(errorMessage, true);
                    }
                }
            });
        });

        deleteButtons.on('click', (e) => {
            var deleteFilmButton = $(e.target);
            filmToDelete = {
                id: deleteFilmButton.data('id'),
                title: deleteFilmButton.data('title')
            }
            showDeleteBox();
        });
    });
</script>