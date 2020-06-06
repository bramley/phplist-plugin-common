<?php
namespace Crossjoin\Css\Format\Rule\Style;

use Crossjoin\Css\Format\Rule\DeclarationAbstract;
use Crossjoin\Css\Helper\Optimizer;

class StyleDeclaration
extends DeclarationAbstract
{
    /**
     * @var bool Importance status
     */
    protected $isImportant = false;

    /**
     * Checks the value.
     *
     * @param string $value
     * @return bool
     */
    public function checkValue(&$value)
    {
        if (parent::checkValue($value)) {
            $value = trim($value, " \r\n\t\f;");

            // Check if declaration contains "!important"
            if (strpos($value, "!") !== false) {
                $charset = $this->getStyleSheet()->getCharset();
                if (mb_strtolower(mb_substr($value, -10, null, $charset), $charset) === "!important") {
                    $this->setIsImportant(true);
                    $value = rtrim(mb_substr($value, 0, -10, $charset));
                }
            }

            // Optimize the value
            $value = Optimizer::optimizeDeclarationValue($value);

            return true;
        }

        return false;
    }

    /**
     * Sets the importance status of the declaration.
     *
     * @param $isImportant
     */
    public function setIsImportant($isImportant)
    {
        if (is_bool($isImportant)) {
            $this->isImportant = $isImportant;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($isImportant). "' for argument 'isImportant' given."
            );
        }
    }

    /**
     * Gets the importance status of the declaration.
     *
     * @return bool
     */
    public function getIsImportant()
    {
        return $this->isImportant;
    }
}