<div class="list">
    <?php if (count($this->getData('films'))) : ?>
        <div id="films-box">
            <?php foreach ($this->getData('films') as $film_data) : ?>
                <div class="film-box">
                    <div class="film-data">
                        <div class="box-row">
                            <span class="id">ID: <?= $film_data['id'] ?></span>
                            <button type="button" class="delete-film" data-id="<?= $film_data['id'] ?>">delete</button>
                        </div>
                        <span class="title">TITLE: <?= $film_data['title'] ?></span>
                        <span class="release-year">RELEASE YEAR: <?= $film_data['release_year'] ?></span>
                        <span class="format">FORMAT: <?= $film_data['format_title'] ?></span>
                    </div>
                    <?php if (!empty($film_data['actors'])) : ?>
                        <div>
                            <span>ACTORS: <?= implode(', ', $film_data['actors']); ?></span>
                        </div>
                    <?php endif ?>
                </div>
            <?php endforeach ?>
        </div>
    <?php else : ?>
        <span class="list-message">NO FILMS FOUNDED</span>
    <?php endif ?>
    <span class="list-message" id="deleting-message" style="display:none;">DELETING...</span>
</div>

<script type="text/javascript">
    $(document).ready(() => {
        var deleteButtons = $('.delete-film');

        deleteButtons.on('click', (e) => {
            var deleteFilmButton = $(e.target);
            $("#films-box").hide();
            $("#deleting-message").show();

            $.ajax({
                type: "POST",
                url: '/film/delete',
                data: {
                    id: deleteFilmButton.data('id')
                },
                complete: (xhr) => {
                    if (xhr.status == 200) {
                        location.href = window.location.href;
                    } else {
                        var errorMessage = xhr.status + ': ' + xhr.statusText
                        alert(errorMessage);
                    }
                }
            });
        });
    });
</script>