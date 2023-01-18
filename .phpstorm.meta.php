<?php

namespace PHPSTORM_META
{
    override(\Psr\Container\ContainerInterface::get(0), map(['' => '@']));
    override(\DI\Container::get(0), map(['' => '@']));
    override(\Southaxis\Helpers\service(0), map(['' => '@']));
}
