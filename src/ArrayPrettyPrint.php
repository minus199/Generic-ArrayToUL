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
        $scriptTag2->setAttribute('src', '../JS/toggler.js');

        $head = $this->dom->createElement('head');
        if ($css = $this->getCSS(!$includeCss))
            $head->appendChild($css);

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

                // hopefully will find the last element with matched class
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
                $li = $this->dom->createElement('li', $k . (is_array($v) ? "" : ": $v"));
                $li->setAttribute('class', 'sub_depth_' . $dataIterator->getDepth());
                $li->setAttribute('ref', 'title_sub_depth');
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

                $li = $this->dom->createElement('li', $dataIterator->key() . ": " . $dataIterator->current());
                $li->setAttribute('class', 'sub_depth_' . $dataIterator->getDepth());
                $ul->appendChild($li);
            }
        }

        return $this;
    }

    public function asHTML($wrapWithPage = false, $includeCSS = false, $includeToggleButton = false)
    {
        if (!$this->unorderedList)
            throw new Exception("Must prettify first!");

        if ($includeToggleButton) {
            $this->generateToggleButton();
        }

        if (!$wrapWithPage)
            return $this->unorderedList->saveHTML();

        $body = $this->dom->createElement('body');
        $body->appendChild($this->unorderedList);
        $this->startHTML($includeCSS)->appendChild($body);

        $this->dom->appendChild($this->html);

        return $this->dom->saveHTML();
    }

    public function getCSS($disable = false)
    {
        if ($disable)
            throw new Exception("CSS was disabled but usage still exists.");

        if ($this->css) return $this->css;

        $cssData = array(
            "\tul { color:#eee; }",
            "\tul { font-size:18px; }",
            "\tul li { }",
            "\tul li { list-style-image: url('''); padding:5px 0 5px 18px; font-size:15px; }",
            "\tul li { color:black; height:23px; margin-left:10px; }"
        );

        return $this->css = new DOMElement('style', implode("\n", $cssData));
    }

    public function setCSS($css)
    {
        $this->css = $css;
        return $this;
    }

    private function generateToggleButton()
    {
        $toggleButton = $this->dom->createElement('input');
        $toggleButton->setAttribute('ref', 'collapsedTrue');
        $toggleButton->setAttribute('type', 'button');
        $toggleButton->setAttribute('value', '+');
        $toggleButton->setAttribute('id', 'toggleTree');
        $this->getUnorderedList()->firstChild->firstChild->appendChild($toggleButton);
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

