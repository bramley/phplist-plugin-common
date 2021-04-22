<?php
namespace Crossjoin\Css\Format\Rule;

trait TraitDeclarations
{
    /**
     * @var array Declaration array
     */
    protected $declarations = [];

    /**
     * Sets the declarations.
     *
     * @param DeclarationAbstract[]|DeclarationAbstract $declarations
     * @return $this
     */
    public function setDeclarations($declarations)
    {
        $this->declarations = [];
        if (!is_array($declarations)) {
            $declarations = [$declarations];
        }
        foreach ($declarations as $declaration) {
            $this->addDeclaration($declaration);
        }

        return $this;
    }

    /**
     * Adds a declaration.
     *
     * @param DeclarationAbstract $declaration
     * @return $this
     */
    public function addDeclaration(DeclarationAbstract $declaration)
    {
        // Overwrite an check for allowed instances
        $this->declarations[] = $declaration;

        return $this;
    }

    /**
     * Gets an array of declarations.
     *
     * @return DeclarationAbstract[]
     */
    public function getDeclarations()
    {
        return $this->declarations;
    }
}