<div id="add-film-modal" class="modal" aria-hidden="true">
  <div class="overlay" tabindex="-1" data-micromodal-close>
    <div class="container fancy-box" role="dialog" aria-modal="true" aria-labelledby="add-film-modal-title">
      <div id="add-film-modal-content">
        <form id="add-film-form">
          <input type="text" name="title" placeholder="FILM TITLE" />
          <input type="number" value="2020" name="year" />
          <select name="format_id">
            <?php foreach ($this->getData('film_formats') as $format_data) : ?>
              <option value=<?= $format_data['id'] ?>><?= $format_data['title'] ?></option>
            <?php endforeach; ?>
          </select>
          <button id="add-actor">add actor</button>
          <div id="actors"></div>
          <button type="submit">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(() => {
    // ajax call
    var filmForm = $('#add-film-form');

    filmForm.submit((e) => {
      e.preventDefault();
      $.ajax({
        type: "POST",
        url: '/film/add',
        data: filmForm.serialize(),
        complete: (xhr) => {
          if (xhr.status == 201) {
            location.href = '/';
          } else {
            var errorMessage = xhr.status + ': ' + xhr.statusText
            alert(errorMessage);
          }
        }
      });
    });

    // handle actors
    var addActorButton = $("#add-actor");
    var actorsDiv = $("#actors");

    addActorButton.click((e) => {
      e.preventDefault();
      var newElement = `
        <div>
          <input type="text" name="actors[]" />
          <button type="button" class="delete-actor" onclick="onClickRemoveActor(this)">remove</button>
        </div>
      `;
      actorsDiv.append(newElement);
    });
  });

  function onClickRemoveActor(target) {
    var deleteButton = $(target);
    deleteButton.parent().remove();
  }
</script>