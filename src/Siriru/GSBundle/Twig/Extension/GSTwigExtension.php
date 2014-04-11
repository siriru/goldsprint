<?php
namespace Siriru\GSBundle\Twig\Extension;

use Siriru\GSBundle\Utility;

class GSTwigExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('gstime', array($this, 'gsTimeFilter')),
        );
    }

    public function gsTimeFilter($float)
    {
        if($float !== null) {
            return Utility::separateFloat($float);
        }
        return '';
    }

    public function getName()
    {
        return 'gs_extension';
    }
}
