<?php

namespace AppBundle\Entity;

use OpenSky\Bundle\RuntimeConfigBundle\Entity\ParameterRepository as BaseParameterRepository;
use Symfony\Component\Yaml\Inline;

class ParameterRepository extends BaseParameterRepository {
    public function getParametersAsKeyValueHash()
    {
        return array_map(
            function($v){ return Inline::parse($v); },
            parent::getParametersAsKeyValueHash()
        );
    }
}
