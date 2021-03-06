<?php

namespace Nodes\Support\UserAgent\Agents;

use Nodes\Exceptions\BadRequestException;

/**
 * Class Nodes.
 */
class Meta
{
    /**
     * @var string
     */
    protected $platform;

    /**
     * @var string
     */
    protected $environment;

    /**
     * Version number.
     *
     * @var string
     */
    protected $version = 0;

    /**
     * Major version number.
     *
     * @var int
     */
    protected $majorVersion = 0;

    /**
     * Minor version number.
     *
     * @var int
     */
    protected $minorVersion = 0;

    /**
     * Patch version number.
     *
     * @var int
     */
    protected $patchVersion = 0;

    /**
     * @var string|null
     */
    protected $deviceOsVersion;

    /**
     * @var string
     */
    protected $device;

    const PLATFORM_IOS = 'ios';

    const PLATFORM_ANDROID = 'android';

    const PLATFORM_WINDOWS = 'windows';

    const PLATFORM_WEB = 'web';

    /**
     * platforms.
     *
     * @var array
     */
    protected $platforms;

    const ENV_DEVELOPMENT = 'development';

    const ENV_STAGING = 'staging';

    const ENV_PRODUCTION = 'production';

    const ENV_LOCAL = 'local';

    /**
     * environments.
     *
     * @var array
     */
    protected $environments;

    /**
     * Meta constructor.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param $header
     *
     * @throws BadRequestException
     */
    public function __construct($header)
    {
        $headerArr = explode(';', $header);

        // Load platforms from config
        $this->platforms = self::getMetaPlatforms();

        // Load environments from config
        $this->environments = self::getMetaEnvironments();

        // Parse platform
        if (!isset($headerArr[0]) || !in_array($headerArr[0], $this->platforms)) {
            throw new BadRequestException('Platform is not supported, should be: '.implode(',', $this->platforms));
        }

        $this->platform = $headerArr[0];

        // Parse env
        if (!isset($headerArr[1]) || !in_array($headerArr[1], $this->environments)) {
            throw new BadRequestException('Environment is not supported, should be: '.implode(',', $this->environments));
        }

        $this->environment = $headerArr[1];

        // Web does not have further requirements, since they have a normal User-Agent header
        if ($this->platform == self::PLATFORM_WEB) {
            return;
        }

        // Parse Build number
        if (!isset($headerArr[2])) {
            throw new BadRequestException('Missing version');
        }

        $this->version = $headerArr[2];
        $versionArr = explode('.', $this->version);
        $this->majorVersion = isset($versionArr[0]) ? $versionArr[0] : 0;
        $this->minorVersion = isset($versionArr[1]) ? $versionArr[1] : 0;
        $this->patchVersion = isset($versionArr[2]) ? $versionArr[2] : 0;

        // Parse device os version
        if (!isset($headerArr[3])) {
            throw new BadRequestException('Missing device os version');
        }

        $this->deviceOsVersion = $headerArr[3];

        // Parse device
        if (!isset($headerArr[4])) {
            throw new BadRequestException('Missing device');
        }

        $this->device = $headerArr[4];
    }

    /**
     * Retrieve platform.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Retrieve environment.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Retrieve version.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Retrieve majorVersion.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return int
     */
    public function getMajorVersion()
    {
        return $this->majorVersion;
    }

    /**
     * Retrieve minorVersion.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return int
     */
    public function getMinorVersion()
    {
        return $this->minorVersion;
    }

    /**
     * Retrieve patchVersion.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return int
     */
    public function getPatchVersion()
    {
        return $this->patchVersion;
    }

    /**
     * Retrieve deviceOsVersion.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return null|string
     */
    public function getDeviceOsVersion()
    {
        return $this->deviceOsVersion;
    }

    /**
     * Retrieve device.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return string
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * toArray.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'platform'        => $this->platform,
            'environment'     => $this->environment,
            'version'         => $this->version,
            'majorVersion'    => $this->majorVersion,
            'minorVersion'    => $this->minorVersion,
            'patchVersion'    => $this->patchVersion,
            'deviceOsVersion' => $this->deviceOsVersion,
            'device'          => $this->device,
        ];
    }

    /**
     * getMetaEnvironments.
     *
     * @author Rasmus Ebbesen <re@nodes.dk>
     *
     * @static
     *
     * @return array
     */
    public static function getMetaEnvironments()
    {
        return array_merge([
            self::ENV_LOCAL,
            self::ENV_DEVELOPMENT,
            self::ENV_STAGING,
            self::ENV_PRODUCTION,
        ], config('nodes.meta.environments', []));
    }

    /**
     * getMetaPlatforms.
     *
     * @author Rasmus Ebbesen <re@nodes.dk>
     *
     * @static
     *
     * @return array
     */
    public static function getMetaPlatforms()
    {
        return array_merge([
            self::PLATFORM_IOS,
            self::PLATFORM_ANDROID,
            self::PLATFORM_WINDOWS,
            self::PLATFORM_WEB,
        ], config('nodes.meta.platforms', []));
    }
}
