<?php

namespace Core;

class View {

    const TEMPLATE_ROOT = __DIR__ . "/../templates/";
    const DEFAULT_TEMPLATE = self::TEMPLATE_ROOT . "default.php";

    protected string $html_template = self::DEFAULT_TEMPLATE;
    protected string $body_template = "";

    private array $data = [];

    public function setData(string $key, $value) {
        $this->data[$key] = $value;
    }

    public function getData(string $key) {
        return $this->data[$key] ?: null;
    }

    /**
     * Return rendered view
     *
     * @return string|false
     */
    public function toHtml() {
        return $this->getRenderedHtml($this->html_template);
    }

    /**
     * Execute php file and return result as string
     *
     * @param  string $tempaltePath
     * @return string|false
     */
    protected function getRenderedHtml(string $tempaltePath) {
        $html = false;
        if (file_exists($tempaltePath)) {
            ob_start();
            include($tempaltePath);
            $html = ob_get_clean();
        }
        return $html;
    }

}
