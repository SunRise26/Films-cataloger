<?php
$key = htmlspecialchars($_GET['s_key']);
?>

<section class="search">
    <form action="/" method="GET" id="search-form" class="row no-gutters">
        <div class="col-9">
            <input type="text" name="s_key" class="input search-input" value="<?= $key ?>" />
            <div class="selectors">
                <select name="s_type" class="input">
                    <?php foreach ($this->getData('search_by_options') as $title => $option) : ?>
                        <option <?= $_GET['s_type'] == $option ? 'selected' : '' ?> value="<?= $option ?>"><?= $title ?></option>
                    <?php endforeach ?>
                </select>
                <select name="s_sort_order" class="input ml-3" value="<?= $_GET['s_sort_order'] ?>">
                    <?php foreach ($this->getData('search_sort_order_oprions') as $title => $option) : ?>
                        <option <?= $_GET['s_sort_order'] == $option ? 'selected' : '' ?> value="<?= $option ?>"><?= $title ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="col-3 pl-3">
            <input type="submit" value="OK" class="input button submit" />
        </div>
    </form>
</section>