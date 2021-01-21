<div id="add-film-modal" class="modal" aria-hidden="true">
  <div id="add-film-modal-overlay" class="overlay" tabindex="-1" data-micromodal-close>
    <div id="add-film-modal-container" class="container fancy-box" role="dialog" aria-modal="true" aria-labelledby="add-film-modal-title">
      <div id="add-film-modal-content">
        <form id="add-film-form">
          <input type="text" name="title" class="input" placeholder="FILM TITLE" />
          <input type="number" value="<?= date('Y') ?>" name="release_year" class="input" />
          <select name="format_id" class="input">
            <?php foreach ($this->getData('film_formats') as $id => $title) : ?>
              <option value=<?= $id ?>><?= $title ?></option>
            <?php endforeach; ?>
          </select>
          <button id="add-actor" class="input button">add actor</button>
          <div id="actors"></div>
          <button type="submit" class="input button">Submit</button>
        </form>
        <div class="message-box" id="add-film-modal-message" style="display:none">
          <span></span>
          <button style="display:none" class="input button" id="add-film-button-reload">continue</button>
          <button style="display:none" class="input button" id="add-film-button-back">back</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(() => {
    const filmForm = $('#add-film-form');
    const modalContainer = $('#add-film-modal-container');
    const modalOverlay = $('#add-film-modal-overlay');

    const messageBox = $('#add-film-modal-message');
    const reloadButton = $("#add-film-button-reload");
    const backButton = $("#add-film-button-back");

    filmForm.on('keypress', function(e) {
      const keyCode = e.keyCode || e.which;
      if (keyCode === 13) {
        e.preventDefault();
        filmForm.submit();
      }
    });

    function setValidationErrors(validationErrors) {
      for (const [inputType, values] of Object.entries(validationErrors)) {
        const prepareErrorBox = (idSubpart) => (`<div class="error-box" id="add-film-${idSubpart}-error"></div>`);
        const prepareErrorBoxText = (id, text) => (`<span class="error-text">${id + 1}: ${text}</span>`);
        const processErrors = (inputElement, idSubpart, errors) => {
          inputElement.after(prepareErrorBox(idSubpart));
          const errorBox = filmForm.find(`#add-film-${idSubpart}-error`);
          errors.forEach((text, id) => {
            errorBox.append(prepareErrorBoxText(id, text));
          });
        };

        if (inputType == 'actors') {
          for (const [inputId, errors] of Object.entries(values)) {
            const inputElement = filmForm.find(`[name="${inputType}[${inputId}]"]`);
            processErrors(inputElement, `${inputType}-${inputId}`, errors);
          }
        } else {
          const inputElement = filmForm.find(`[name="${inputType}"]`);
          processErrors(inputElement, inputType, values);
        }
      }
    }

    function removeAllValidationErrors() {
      const errorBoxes = filmForm.find('.error-box');
      errorBoxes.remove();
    }

    function displayMessage(message, isFinal = false) {
      disableModalClose();
      filmForm.hide();
      messageBox.find('span').html(message);
      if (isFinal) {
        reloadButton.show();
        backButton.hide();
      } else {
        reloadButton.hide();
        backButton.show();
      }
      messageBox.show();
    }

    function displayForm() {
      enableModalClose();
      messageBox.hide();
      filmForm.show();
    }

    function disableModalClose() {
      modalOverlay.removeAttr('data-micromodal-close');
    }

    function enableModalClose() {
      modalOverlay.attr('data-micromodal-close', true);
    }

    reloadButton.click((e) => {
      location.href = window.location.href;
    });

    backButton.click((e) => {
      displayForm();
    });

    filmForm.submit((e) => {
      e.preventDefault();
      removeAllValidationErrors();
      const submitButton = filmForm.find(`[type="submit"]`);
      submitButton.prop('disabled', true);

      $.ajax({
        type: "POST",
        url: '/film/add',
        data: filmForm.serialize(),
        complete: (xhr) => {
          const data = xhr.responseJSON;

          switch (xhr.status) {
            case 422:
              setValidationErrors(data.data);
              const firstInputError = $('.error-box').first().prev();
              modalContainer.animate({
                scrollTop: firstInputError.position().top
              }, 800);
              displayForm();
              break;
            case 200:
              displayMessage(data.message);
              break;
            case 201:
              displayMessage(data.message, true);
              break;
            default:
              displayMessage('something gone wrong :(', true);
          }
          submitButton.prop('disabled', false);
        }
      });
    });

    // handle actors
    const addActorButton = $("#add-actor");
    const actorsDiv = $("#actors");
    var currentActorId = 1;

    function removeButtonAction(e) {
      const element = $(e.target);
      element.parent().remove();
    }

    addActorButton.click((e) => {
      e.preventDefault();
      const newElement = `
        <div>
          <input type="text" name="actors[${currentActorId}]" class="input" />
          <button type="button" class="delete-actor input button" id="remove-actor-${currentActorId}">remove</button>
        </div>
      `;
      actorsDiv.prepend(newElement);
      const removeButton = $(`#remove-actor-${currentActorId}`);
      removeButton.click((e) => removeButtonAction(e));
      currentActorId++;
    });
  });
</script>