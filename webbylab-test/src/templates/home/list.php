<div class="list">
    <?php if (count($this->getData('films'))) : ?>
        <?php foreach ($this->getData('films') as $film_data) : ?>
            <div class="film-box">
                <div class="data-row">
                    <div class="film-data">
                        <span class="title"><?= $film_data['title'] ?></span>
                        <span><?= $film_data['release_year'] ?></span>
                        <span><?= $film_data['format_title'] ?></span>
                    </div>
                    <button type="button" class="delete-film" data-id="<?= $film_data['id'] ?>">delete</button>
                </div>
                <?php if (!empty($film_data['actors'])) : ?>
                    <div class="data-row">
                        <span>Actors: <?= implode(', ', $film_data['actors']); ?></span>
                    </div>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    <?php else : ?>
        <span class="empty-list-message">NO FILMS</span>
    <?php endif ?>
</div>

<script type="text/javascript">
    $(document).ready(() => {
        var deleteButtons = $('.delete-film');

        deleteButtons.on('click', (e) => {
            var deleteFilmButton = $(e.target);

            $.ajax({
                type: "POST",
                url: '/film/delete',
                data: {id: deleteFilmButton.data('id')},
                complete: (xhr) => {
                    if (xhr.status == 200) {
                        location.href = '/';
                    } else {
                        var errorMessage = xhr.status + ': ' + xhr.statusText
                        alert(errorMessage);
                    }
                }
            });
        });
    });
</script>