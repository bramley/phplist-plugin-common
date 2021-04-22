<?php
namespace Crossjoin\Css\Format\Rule;

trait TraitVendorPrefix
{
    protected $vendorPrefix;

    /**
     * Sets the vendor prefix.
     *
     * @param $vendorPrefix
     */
    public function setVendorPrefix($vendorPrefix)
    {
        if (is_string($vendorPrefix)) {
            $this->vendorPrefix = $vendorPrefix;
        } else {
            throw new \InvalidArgumentException(
                "Invalid type '" . gettype($vendorPrefix). "' for argument 'vendorPrefix' given."
            );
        }
    }

    /**
     * Gets the vendor prefix.
     *
     * @return string|null
     */
    public function getVendorPrefix()
    {
        return $this->vendorPrefix;
    }

    /**
     * Returns an array of known vendor prefixes.
     *
     * @see http://www.w3.org/TR/CSS21/syndata.html#vendor-keywords
     * @return string[]
     */
    public static function getVendorPrefixValues()
    {
        return [
            "-ms-",     // Microsoft
            "mso-",     // Microsoft
            "-moz-",    // Mozilla
            "-o-",      // Opera Software
            "-xv-",     // Opera Software
            "-atsc-",   // Advanced Television Standards Committee
            "-wap-",    // The WAP Forum
            "-khtml-",  // KDE
            "-webkit-", // Apple
            "prince-",  // YesLogic
            "-ah-",     // Antenna House
            "-hp-",     // Hewlett Packard
            "-ro-",     // Real Objects
            "-rim-",    // Research In Motion
            "-tc-",     // TallComponents
        ];
    }

    /**
     * Returns a partial regular expression of known vendor prefixes.
     *
     * @param string|null $delimiter
     * @return string
     */
    public static function getVendorPrefixRegExp($delimiter = null)
    {
        $vendorPrefixValues = self::getVendorPrefixValues();
        foreach ($vendorPrefixValues as $vendorPrefixKey => $vendorPrefixValue) {
            $vendorPrefixValues[$vendorPrefixKey] = preg_quote($vendorPrefixValue, $delimiter);
        }
        return implode('|', $vendorPrefixValues);
    }
}