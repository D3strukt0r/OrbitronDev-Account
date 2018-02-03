<?php

use EasyCorp\Bundle\EasyDeployBundle\Deployer\DefaultDeployer;

return new class extends DefaultDeployer
{
    public function configure()
    {
        return $this->getConfigBuilder()
            ->server('user@hostname')
            ->deployDir('/var/www/service-account')
            ->repositoryUrl('git@github.com:OrbitronDev/service-account.git')
            ->repositoryBranch('master')
            ;
    }
};
