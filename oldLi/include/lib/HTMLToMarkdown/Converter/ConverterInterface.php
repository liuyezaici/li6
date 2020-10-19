<?php


interface ConverterInterface
{
    /**
     * @param ElementInterface $element
     *
     * @return string
     */
    public function convert(ElementInterface $element);

    /**
     * @return string[]
     */
    public function getSupportedTags();
}
