<?php

namespace MiNuS199;
/**
 * Created by PhpStorm.
 * User: minus
 * Date: 5/9/15
 * Time: 2:00 AM
 */
class ArrayPrettyPrint
{
    /**
     * @var \DOMDocument
     */
    private $unorderedList;
    /**
     * @var \DOMElement
     */
    private $html;
    private $dom;

    private $data;

    protected $css = null;

    public function __construct($id = 'recursive')
    {
        $this->dom = new \DOMDocument('', 'utf-8');
        $this->unorderedList = $this->dom->createElement('ul');
        $this->unorderedList->setAttribute('id', $id);
    }

    private function startHTML($includeCss)
    {
        $this->html = $this->dom->createElement('html');

        $this->html
            ->appendChild($this->createHead($includeCss));

        return $this->html;
    }

    /**
     * @return \DOMDocument
     */
    public function getUnorderedList()
    {
        return $this->unorderedList;
    }

    public static function factory($data = array())
    {
        $instance = new self();
        return $instance->setData($data);
    }

    /**
     * @return $this|bool
     * @throws \Exception
     */
    public function prettify()
    {
        if (!$data = $this->getData())
            throw new \Exception("Please use ->setData(\$array) first.");

        $parentContainer = 'parent_container';

        $ulMainContainer = new \DOMElement('ul');

        $this->unorderedList->appendChild($ulMainContainer);
        $ulMainContainer->setAttribute('id', $parentContainer);
        $ulMainContainer->setAttribute('class', 'depth_0');

        if (!is_array($data) || empty($data))
            return false;

        $previousDepth = 0;
        $className = "depth_0";

        $dataIterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($data),
            \RecursiveIteratorIterator::SELF_FIRST,
            \RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        foreach ($dataIterator as $k => $v) {
            if ($dataIterator->callHasChildren()) {
                /* The following code is only in order to know which parent should the new ul be appended to */

                if ($dataIterator->getDepth() > 0) {
                    $className = "depth_" . ($dataIterator->getDepth() - 1);
                    $matchedClass = array();
                    foreach ($this->unorderedList->getElementsByTagName("ul") as $node) {
                        /* @var $node \DOMElement */
                        if ($node->getAttribute('class') == $className)
                            $matchedClass[] = $node;
                    }
                }

                $currentParent = array_pop($matchedClass);
                if (!$currentParent /*|| $dataIterator->getDepth() == $previousDepth*/) {
                    $ul = $this->dom->createElement("ul");
                    $ul->setAttribute('class', 'depth_' . $dataIterator->getDepth());
                    $currentParent = $currentParent ? $currentParent : $ul;
                }

                if ($dataIterator->getDepth() == 0)
                    $currentParent = $ulMainContainer;

                /* This will append the new ul to matched parent */
                /* Both the title and the new group are appended to the current parent */

                /* The title of the group */
                $valueContainer = $this->dom->createElement('span', ucfirst($k));
                if (is_array($v))
                    if (empty($v)){
                        $codeElement = $this->dom->createElement("code" , "[Empty]");
                        $valueContainer->appendChild($codeElement);
                    }

                $li = $this->dom->createElement('li');
                $li->setAttribute('class', 'sub_depth_' . $dataIterator->getDepth());
                $li->setAttribute('ref', 'title_sub_depth');
                $li->appendChild($valueContainer);
                $currentParent->appendChild($li);

                /* new group container */
                /* @var $ul \DOMElement */
                $ul = $this->dom->createElement('ul');
                $ul->setAttribute('class', 'depth_' . $dataIterator->getDepth());
                $currentParent->appendChild($ul);

                /* keep current depth as previous depth for next iteration */
                $previousDepth = $dataIterator->getDepth();
            } else { /* Childless elements */
                $ul = $this->unorderedList->getElementsByTagName('ul')->item($this->unorderedList->getElementsByTagName('ul')->length - 1);

                /* if current child element is childless but also last element in current project, a new group should be created */
                $isDeeper = $previousDepth < $dataIterator->getDepth();
                if (!$isDeeper) {
                    $className = "depth_" . ($dataIterator->getDepth() - 1);
                    $matchedClass = array();
                    foreach ($this->unorderedList->getElementsByTagName("ul") as $node) {
                        /* @var $node \DOMElement */
                        if ($node->getAttribute('class') == $className)
                            $matchedClass[] = $node;
                    }

                    $ulNew = array_pop($matchedClass);
                    $ul = $ulNew ? $ulNew : $ul;
                }

                $li = $this->dom->createElement('li');
                $liText = $this->dom->createTextNode($dataIterator->key() . ": " . ($dataIterator->current() !== null && $dataIterator->current() !== false ? $dataIterator->current() : "N/A"));
                $li->appendChild($liText);
                $li->setAttribute('class', 'sub_depth_' . $dataIterator->getDepth() . " regular-item");
                $li->setAttribute('depth', $dataIterator->getDepth());
                $ul->appendChild($li);
            }
        }

        return $this;
    }

    public function asHTML($wrapWithPage = false, $includeCSS = false, $includeToggleButton = false, $prettifyHTML = true)
    {
        if (!$this->unorderedList)
            throw new \Exception("Must prettify first!");


        if (!$wrapWithPage)
            return $this->unorderedList->saveHTML();

        $body = $this->dom->createElement('body');

        $droppableContainer = $this->dom->createElement('ul');
        $droppableContainer->setAttribute('id', 'tempContainer');
        $body->appendChild($droppableContainer);

        if ($includeToggleButton) {
            $body->appendChild($this->generateToggleButton());
        }

        $body->appendChild($this->unorderedList);
        $this->startHTML($includeCSS)->appendChild($body);

        $this->dom->appendChild($this->html);

        if ($prettifyHTML) {
            $this->dom->formatOutput = true;
            $this->dom->preserveWhiteSpace = false;
            $opts = array(
                'indent' => TRUE,
                'input-xml' => TRUE,
                'output-html' => TRUE,
                'add-xml-space' => FALSE,
                'indent-spaces' => 4
            );

            return tidy_parse_string($this->dom->saveHTML(), $opts);
        }

        return $this->dom->saveHTML();
    }

    public function getCSS()
    {
        if ($this->css) return $this->css;

        $dirName = dirname(__DIR__) . DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR . "CSS";
        $output = array();
        foreach (scandir($dirName) as $cssFile){
            if ($cssFile == "." || $cssFile == "..")
                continue;

            $output[] = file_get_contents($dirName . DIRECTORY_SEPARATOR . $cssFile);
        }

        $response = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', trim(implode("\n\n", array_unique($output))));
        return $this->dom->createElement('style', $response);
    }

    public function setCSS($css)
    {
        $this->css = $css;
        return $this;
    }

    private function generateToggleButton()
    {
        $toggleButton = $this->dom->createElement('input');
        $toggleButton->setAttribute('class', 'collapsedTrue');
        $toggleButton->setAttribute('type', 'button');
        $toggleButton->setAttribute('value', '+');
        $toggleButton->setAttribute('id', 'toggleTree');
        $toggleButton->setAttribute('style', 'position:fixed;');

        return $toggleButton;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public static function getTogglerJS(){
        $parts = array(dirname(__DIR__), "Resources", "JS", "toggler.js");
        return "window.onload = function(){" . file_get_contents(implode(DIRECTORY_SEPARATOR, $parts)) . "}";
    }

    public function createHead($includeCss = true, $title = "PrettyPrinted"){
        $head = $this->dom->createElement('head');
        $head->appendChild(new \DOMElement('title', $title));

        /* CSS */
        if ($css = $this->getCSS($includeCss))
            $head->appendChild($css);

        /* Vendor */
        $scriptTag1 = $this->dom->createElement('script');
        $scriptTag1->setAttribute('src', '//code.jquery.com/jquery-2.0.3.js');
        $scriptTag2 = $this->dom->createElement('script');
        $scriptTag2->setAttribute('src', '//code.jquery.com/ui/1.11.4/jquery-ui.js');

        /* Local */
        $scriptTag3 = $this->dom->createElement('script', $this::getTogglerJS());

        foreach (range(1,3) as $i){
            $element = 'scriptTag' . $i;
            if ($element) $head->appendChild($$element);
        }

        return $head;
    }
}

