<?php

namespace T3ac\Person\ViewHelpers;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class EmptyFieldViewHelper extends AbstractViewHelper {
    
    public function initializeArguments(): void
    {
        $this->registerArgument('key', 'string', '', true, 'NULL');
     
    }  
    
    
    /**
     * ViewHelper to strip blanks and empty spaces from the key
     * 
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string|unknown
     *
     */
    
    
    public function render()
    {
        if ($this->arguments['key'] === NULL){
            return '';
        }
        else {
            $keyString = $this->arguments['key'];
            $keyOutput = preg_replace('/[ ]*/i', "", $keyString);
            
            return $keyOutput;
         }
    }
}
