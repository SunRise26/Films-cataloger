<section class="search">
    <form action="/" method="GET" id="search-form">
        <input type="text" name="s_key" class="input" value="<?= $_GET['s_key'] ?>" />
        <select name="s_type" class="input">
            <?php foreach ($this->getData('search_by_options') as $title => $option): ?>
                <option <?= $_GET['s_type'] == $option ? 'selected' : '' ?> value="<?= $option ?>"><?= $title ?></option>
            <?php endforeach ?>
        </select>
        <input type="submit" value="OK" class="input button" />
    </form>
</section>
