<?php

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
     * @var DOMElement
     */
    private $html;
    private $dom;

    private $data;

    protected $css = null;

    public function __construct($id = 'recursive')
    {
        $this->dom = new DOMDocument('', 'utf-8');
        $this->unorderedList = $this->dom->createElement('ul');
        $this->unorderedList->setAttribute('id', $id);
    }

    private function startHTML($includeCss)
    {
        $this->html = $this->dom->createElement('html');

        $scriptTag = $this->dom->createElement('script');
        $scriptTag->setAttribute('src', 'http://code.jquery.com/jquery-2.0.3.js');

        $scriptTag2 = $this->dom->createElement('script');
        $scriptTag2->setAttribute('src', '../../Resources/JS/toggler.js');

        $head = $this->dom->createElement('head');
        if ($css = $this->getCSS(!$includeCss))
            foreach ($css as $file)
                $head->appendChild($file);

        $head->appendChild($scriptTag);
        $head->appendChild($scriptTag2);

        $this->html
            ->appendChild($head)
            ->appendChild(new DOMElement('title', 'output html'));

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
                        /* @var $node DOMElement */
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
                        /* @var $node DOMElement */
                        if ($node->getAttribute('class') == $className)
                            $matchedClass[] = $node;
                    }

                    $ulNew = array_pop($matchedClass);
                    $ul = $ulNew ? $ulNew : $ul;
                }

                $li = $this->dom->createElement('li', $dataIterator->key() . ": " . ($dataIterator->current() ? $dataIterator->current() : "N/A"));
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
            throw new Exception("Must prettify first!");


        if (!$wrapWithPage)
            return $this->unorderedList->saveHTML();

        $body = $this->dom->createElement('body');
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

            $link = $this->dom->createElement('link');
            $link->setAttribute('rel', 'stylesheet');
            $link->setAttribute('type', 'text/css');
            $link->setAttribute('href', '../../Resources/CSS/' . $cssFile);
            $output[] = $link;
        }

        return $output;
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
}

