<section class="search">
    <form action="/" method="GET">
        <input type="text" name="s_key" value="<?= $_GET['s_key'] ?>" />
        <select name="s_type">
            <?php foreach ($this->getData('search_by_options') as $title => $option): ?>
                <option <?= $_GET['s_type'] == $option ? 'selected' : '' ?> value="<?= $option ?>"><?= $title ?></option>
            <?php endforeach ?>
        </select>
        <input type="submit" value="submit" />
    </form>
</section>
