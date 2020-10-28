<?php

namespace app\components;

use yii\widgets\LinkSorter;

class LinkSorterTHead extends LinkSorter {

    public $attributesLinkFalse = [];
    public $colspans = [];

    /**
     * Executes the widget.
     * This method renders the sort links.
     */
    public function run() {
        echo $this->renderSortLinks();
    }

    /**
     * Renders the sort links.
     * @return string the rendering result
     */
    protected function renderSortLinks() {
        

        $attributes = empty($this->attributes) ? array_keys($this->sort->attributes) : $this->attributes;
        $result = '';
        $arFlip = array_flip($this->attributes);
        foreach ($attributes as $name) {
            $tag = 'a';
            $result .= '<th ' . ((strpos($arFlip[$name], '=') !== false) ? $arFlip[$name] : 'class="' . $arFlip[$name] . '"') . ' colspan="' . (isset($this->colspans[$name]) ? $this->colspans[$name] : 1) . '">' . (
                    (isset($this->attributesLinkFalse[$name]) and $this->attributesLinkFalse[$name] === false) ? '<a>' . $this->sort->attributes[$name]['label'] . '</a>' : $this->sort->link($name, $this->linkOptions)
                    ) . "</th>";
        }

        return '<tr>' . $result . '</tr>';
    }

}
