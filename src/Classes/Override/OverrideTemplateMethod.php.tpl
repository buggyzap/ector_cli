    // type an array here with all supported versions of this method, replace the version numbers with the versions you are overriding
    #[Ector\Core\EctorOverrideAlias([
    '0.0.1' => ['0.0.2']
    ])]
    public function {{methodName}}()
    {
        // don't alter this method, this calls the correct version of the method
        return $this->overrideMethod(__FUNCTION__, $this->version);
    }

    // this method is override-version specific, replace the version number with the version you are overriding
    public function {{methodName}}_0_0_1()
    {

    }